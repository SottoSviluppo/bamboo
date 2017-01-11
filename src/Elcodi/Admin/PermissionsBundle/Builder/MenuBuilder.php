<?php

namespace Elcodi\Admin\PermissionsBundle\Builder;

use Elcodi\Component\Menu\Builder\Abstracts\AbstractMenuBuilder;
use Elcodi\Component\Menu\Builder\Interfaces\MenuBuilderInterface;
use Elcodi\Component\Menu\Entity\Menu\Interfaces\MenuInterface;
use Elcodi\Component\Menu\Factory\NodeFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MenuBuilder extends AbstractMenuBuilder implements MenuBuilderInterface
{
    private $permissionsRepository;
    private $currentUser;
    private $resource = "permissions";
    private $permissions = [
        'canRead' => false,
        'canCreate' => false,
        'canUpdate' => false,
        'canDelete' => false
    ];

    function __construct(NodeFactory $menuNodeFactory, ContainerInterface $container)
    {
        parent::__construct($menuNodeFactory);
        $this->permissionsRepository = $container->get('elcodi.repository.permission_group');
        $this->currentUser = $container->get('security.token_storage')->getToken()->getUser();

        $this->permissions = [
            'canRead' => $this->permissionsRepository->canReadEntity($this->resource, $this->currentUser),
            'canCreate' => $this->permissionsRepository->canCreateEntity($this->resource, $this->currentUser),
            'canUpdate' => $this->permissionsRepository->canUpdateEntity($this->resource, $this->currentUser),
            'canDelete' => $this->permissionsRepository->canDeleteEntity($this->resource, $this->currentUser)
        ];
    }

    /**
     * Build the menu
     *
     * @param MenuInterface $menu Menu
     */
    public function build(MenuInterface $menu)
    {
        if ($this->hasAnyPermissions()) {
            $permissionsNode = $this
                                    ->menuNodeFactory
                                    ->create()
                                    ->setName('admin.permissions.plural')
                                    ->setCode('key')
                                    ->setTag('settings')
                                    ->setPriority(32);
                                    /*->setActiveUrls([
                                        'admin_permissions_edit',
                                        'admin_permissions_new',
                                    ])*/

            if ($this->permissions['canRead']) {
                $permissionsNode->setUrl('admin_permissions_list');
            }
            elseif ($this->permissions['canCreate']) {
                $permissionsNode->setUrl('admin_permissions_new');
            }

            $menu->addSubnode($permissionsNode);
        }
    }

    private function hasAnyPermissions()
    {
        foreach ($this->permissions as $key => $value) {
            if ($value) {
                return true;
            }
        }

        return false;
    }
}