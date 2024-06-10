<?php

/*
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014-2016 Elcodi.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author Aldo Chiecchia <zimage@tiscali.it>
 * @author Elcodi Team <tech@elcodi.com>
 */

namespace Elcodi\Admin\SocialBundle\Builder;

use Elcodi\Component\Menu\Builder\Abstracts\AbstractMenuBuilder;
use Elcodi\Component\Menu\Builder\Interfaces\MenuBuilderInterface;
use Elcodi\Component\Menu\Entity\Menu\Interfaces\MenuInterface;
use Elcodi\Component\Menu\Factory\NodeFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MenuBuilder
 */
class MenuBuilder extends AbstractMenuBuilder implements MenuBuilderInterface
{
    private $permissionsRepository;
    private $currentUser;
    private $resource = "coupon";
    private $permissions = [
        'canRead' => false,
        'canCreate' => false,
        'canUpdate' => false,
        'canDelete' => false,
    ];

    public function __construct(NodeFactory $menuNodeFactory, ContainerInterface $container)
    {
        parent::__construct($menuNodeFactory);
        $this->permissionsRepository = $container->get('elcodi.repository.permission_group');
        $this->currentUser = $container->get('security.token_storage')->getToken()->getUser();

        $this->permissions = [
            'canRead' => $this->permissionsRepository->canReadEntity($this->resource, $this->currentUser),
            'canCreate' => $this->permissionsRepository->canCreateEntity($this->resource, $this->currentUser),
            'canUpdate' => $this->permissionsRepository->canUpdateEntity($this->resource, $this->currentUser),
            'canDelete' => $this->permissionsRepository->canDeleteEntity($this->resource, $this->currentUser),
        ];
    }

    /**
     * Build the menu
     *
     * @param MenuInterface $menu Menu
     */
    public function build(MenuInterface $menu)
    {
        if ($this->hasAnyPermission()) {
            $menu
                ->findSubnodeByName('admin.communication.single')
                ->addSubnode(
                    $this
                        ->menuNodeFactory
                        ->create()
                        ->setName('admin.social.plural')
                        ->setCode('file-text-o')
                        ->setUrl('admin_social_list')
                        ->setActiveUrls([
                            'admin_social_edit',
                            'admin_social_new',
                        ])
                        ->setPriority(-32)
                );
        }
    }

    private function hasAnyPermission()
    {
        foreach ($this->permissions as $key => $value) {
            if ($value) {
                return true;
            }
        }

        return false;
    }
}
