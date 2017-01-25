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

namespace Elcodi\Admin\PluginBundle\Builder;

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

    private $canViewAppStore;

    public function __construct(NodeFactory $menuNodeFactory, ContainerInterface $container)
    {
        parent::__construct($menuNodeFactory);
        $this->permissionsRepository = $container->get('elcodi.repository.permission_group');
        $this->currentUser = $container->get('security.token_storage')->getToken()->getUser();

        $this->canViewAppStore = $this->permissionsRepository->canViewAppStore($this->currentUser);
    }

    /**
     * Build the menu
     *
     * @param MenuInterface $menu Menu
     */
    public function build(MenuInterface $menu)
    {
        $pluginNode = $this
            ->menuNodeFactory
            ->create()
            ->setName('admin.plugin.plural')
            ->setCode('puzzle-piece')
            ->setUrl('admin_plugin_list')
            ->setPriority(-30)
            ->setTag('settings')
            ->setActiveUrls([
                'admin_plugin_configure',
            ]);

        if ($this->canViewAppStore) {
            $pluginNode->addSubnode(
                $this
                    ->menuNodeFactory
                    ->create()
                    ->setName('admin.plugin.app_store')
                    ->setUrl('admin_plugin_list')
                    ->setPriority(9999)
            );
        }

        $menu
            ->addSubnode($pluginNode)
            ->addSubnode(
                $this
                    ->menuNodeFactory
                    ->create()
                    ->setName('plugin_type.social')
                    ->setCode('share-alt')
                    ->setTag('settings')
                    ->setPriority(32)
            );
    }
}
