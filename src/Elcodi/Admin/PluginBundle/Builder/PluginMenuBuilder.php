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

use Elcodi\Component\Menu\Builder\Interfaces\MenuBuilderInterface;
use Elcodi\Component\Menu\Entity\Menu\Interfaces\MenuInterface;
use Elcodi\Component\Menu\Entity\Menu\Interfaces\NodeInterface;
use Elcodi\Component\Menu\Factory\NodeFactory;
use Elcodi\Component\Plugin\Entity\Plugin;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PluginMenuBuilder
 */
class PluginMenuBuilder implements MenuBuilderInterface
{
    /**
     * @var NodeFactory
     *
     * Menu node factory
     */
    protected $menuNodeFactory;

    /**
     * @var Plugin[]
     *
     * Plugins configuration
     */
    protected $enabledPlugins;

    private $permissionsRepository;
    private $currentUser;

    private $canViewAppStore;

    /**
     * Constructor
     *
     * @param NodeFactory $menuNodeFactory Menu node factory
     * @param array       $enabledPlugins  Enabled Plugins
     */
    public function __construct(
        NodeFactory $menuNodeFactory,
        array $enabledPlugins,
        ContainerInterface $container
    ) {
        $this->menuNodeFactory = $menuNodeFactory;
        $this->enabledPlugins = $enabledPlugins;

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
        if ($this->canViewAppStore) {
            $visiblePlugins = $this->filterVisiblePlugins();

            $this
                ->buildByPluginCategory(
                    $menu->findSubnodeByName('plugin_type.payment'),
                    $visiblePlugins,
                    'payment'
                )
                ->buildByPluginCategory(
                    $menu->findSubnodeByName('plugin_type.shipping'),
                    $visiblePlugins,
                    'shipping'
                )
                ->buildByPluginCategory(
                    $menu->findSubnodeByName('plugin_type.social'),
                    $visiblePlugins,
                    'social'
                );
        }
    }

    /**
     * Build by category and place all menu entries inside a family
     *
     * @param NodeInterface $parentNode     Parent menu node
     * @param Plugin[]      $plugins        Plugins
     * @param string        $pluginCategory Plugin category
     *
     * @return $this Self object
     */
    private function buildByPluginCategory(
        NodeInterface $parentNode,
        array $plugins,
        $pluginCategory
    ) {
        if ($this->canViewAppStore) {
            foreach ($plugins as $plugin) {
                if ($plugin->getCategory() !== $pluginCategory) {
                    continue;
                }

                $node = $this
                    ->menuNodeFactory
                    ->create()
                    ->setName($plugin->getConfigurationValue('name'))
                    ->setCode($plugin->getConfigurationValue('fa_icon'))
                    ->setUrl([
                        'admin_plugin_configure', [
                            'pluginHash' => $plugin->getHash(),
                        ],
                    ])
                    ->setEnabled(true);

                $parentNode->addSubnode($node);
            }

            return $this;
        }
    }

    /**
     * Return only visible plugins
     *
     * @return Plugin[] Visible plugins
     */
    protected function filterVisiblePlugins()
    {
        return array_filter(
            $this->enabledPlugins,
            function (Plugin $plugin) {

                return
                    $plugin->getConfigurationValue('visible') &&
                    $plugin->hasFields();
            }
        );
    }
}
