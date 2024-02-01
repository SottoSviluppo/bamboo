<?php

namespace Elcodi\Admin\UserBundle\Security;

use Elcodi\Component\User\Entity\AdminUser;
use Elcodi\Component\User\Entity\Interfaces\AdminUserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AdminUserProvider implements UserProviderInterface
{
    /**
     * @var mixed
     */
    private $adminUserRepository;

    public function __construct($adminUserRepository)
    {
        $this->adminUserRepository = $adminUserRepository;
    }

    public function loadUserByUsername($username)
    {
        return $this->fetchUser($username);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof AdminUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        $username = $user->getUsername();

        return $this->fetchUser($username);
    }

    public function supportsClass($class)
    {
        return AdminUser::class === $class;
    }

    private function fetchUser(string $username)
    {
        $user = $this->adminUserRepository->findOneBy(['email' => $username, 'enabled' => true]);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" non trovato.', $username));
        }

        if (!$user instanceof AdminUserInterface) {
            throw new \InvalidArgumentException(sprintf('La classe "%s" deve implementare l\'interfaccia "UserInterface".', get_class($user)));
        }

        if (!$user->isEnabled()) {
            throw new \InvalidArgumentException('L\'utente è disabilitato.');
        }

        return $user;
    }
}