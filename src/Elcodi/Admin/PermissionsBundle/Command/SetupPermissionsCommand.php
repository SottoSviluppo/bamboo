<?php

namespace Elcodi\Admin\PermissionsBundle\Command;

use Elcodi\Component\Core\Command\Abstracts\AbstractElcodiCommand;
use Elcodi\Component\Permissions\Entity\Permission;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupPermissionsCommand extends AbstractElcodiCommand
{
    protected $container;

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('elcodi:permissions:setup')
            ->setDescription('Setup the permissions for the standard admin user');
        $this->addArgument('email', InputArgument::OPTIONAL, 'Admin email address');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->getApplication()->getKernel()->getContainer();

        $this->startCommand($output)->createData($input, $output)->finishCommand($output);
    }

    protected function createData(InputInterface $input, OutputInterface $output)
    {
        $this->printMessage($output, 'Permissions setup', 'Starting');

        try {
            $adminUserRepository = $this->container->get('elcodi.repository.admin_user');
            $permissionGroupFactory = $this->container->get('elcodi.factory.permission_group');
            $permissionGroupManager = $this->container->get('elcodi.object_manager.permission_group');

            $defaultEmail = 'admin@sottosviluppo.com';
            if ($input->getArgument('email')) {
                $defaultEmail = $input->getArgument('email');
            }

            $adminUser = $adminUserRepository->findOneBy(['email' => $defaultEmail]);

            if ($adminUser != null) {
                $this->printMessage($output, 'Permissions setup', 'Set permissions to ' . $defaultEmail);
                $userFullName = trim($adminUser->getFirstname().' '.$adminUser->getLastname());
                if (empty($userFullName)) {
                    $userFullName = $defaultEmail;
                }

                $name = 'Full permissions - '.$userFullName;

                $permissionGroup = $permissionGroupFactory
                    ->create()
                    ->setName($name)
                    ->setAdminUser($adminUser)
                    ->setViewStore(true)
                    ->setViewShipping(true)
                    ->setViewAppStore(true);

                $resources = $this->container->getParameter('permissions_list');
                foreach ($resources as $key => $value) {
                    $permission = new Permission();
                    $permission->setResource($key)
                        ->setCanRead(true)
                        ->setCanCreate(true)
                        ->setCanUpdate(true)
                        ->setCanDelete(true);

                    $permissionGroup->addPermission($permission);
                }

                $permissionGroupManager->persist($permissionGroup);
                $permissionGroupManager->flush($permissionGroup);
            }

        } catch (\Exception $ex) {
            $this->printMessage(
                $output,
                'Error',
                $ex->getMessage() . " in " . $ex->getFile() . " on line " . $ex->getLine() . " - " . $ex->getTraceAsString()
            );
        }

        return $this;
    }
}
