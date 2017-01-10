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

namespace Elcodi\Admin\ProductBundle\Builder;

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
    private $resources = ['category', 'product', 'manufacturer', 'attribute', 'pack'];
    private $permissions = [];

    function __construct(NodeFactory $menuNodeFactory, ContainerInterface $container)
    {
        parent::__construct($menuNodeFactory);
        $this->permissionsRepository = $container->get('elcodi.repository.permission_group');
        $this->currentUser = $container->get('security.token_storage')->getToken()->getUser();

        $this->setPermissions();
    }

    /**
     * Build the menu
     *
     * @param MenuInterface $menu Menu
     */
    public function build(MenuInterface $menu)
    {
        $this->setProductMenu($menu)
            ->setPackMenu($menu)
            ->setCategorizationMenu($menu);
    }

    private function setProductMenu(MenuInterface $menu)
    {
        if ($this->hasAnyPermissions('product')) {
            $activeUrls = [];
            $productNode = $this
                                ->menuNodeFactory
                                ->create()
                                ->setName('admin.product.plural')
                                ->setCode('barcode')
                                ->setTag('catalog')
                                ->setPriority(35);

            if ($this->permissions['product']['canRead']) {
                $productNode->setUrl('admin_product_list');
            }
            elseif ($this->permissions['product']['canCreate']) {
                $productNode->setUrl('admin_product_new');
            }

            if ($this->permissions['product']['canCreate']) {
                $activeUrls[] = 'admin_product_new';
            }

            if ($this->permissions['product']['canUpdate']) {
                $activeUrls[] = 'admin_product_edit';
            }

            if (!empty($activeUrls)) {
                $productNode->setActiveUrls($activeUrls);
            }

            $menu->addSubnode($productNode);
        }
        
        return $this;
    }

    private function setPackMenu(MenuInterface $menu)
    {
        if ($this->hasAnyPermissions('pack')) {
            $activeUrls = [];
            $packNode = $this
                            ->menuNodeFactory
                            ->create()
                            ->setName('admin.purchasable_pack.plural')
                            ->setCode('archive')
                            ->setTag('catalog')
                            ->setPriority(33);

            if ($this->permissions['pack']['canRead']) {
                $packNode->setUrl('admin_purchasable_pack_list');
            }
            elseif ($this->permissions['pack']['canCreate']) {
                $packNode->setUrl('admin_purchasable_pack_new');
            }

            if ($this->permissions['pack']['canCreate']) {
                $activeUrls[] = 'admin_purchasable_pack_new';
            }

            if ($this->permissions['pack']['canUpdate']) {
                $activeUrls[] = 'admin_purchasable_pack_edit';
            }

            if (!empty($activeUrls)) {
                $packNode->setActiveUrls($activeUrls);
            }

            $menu->addSubnode($packNode);
        }
        
        return $this;
    }

    private function setCategorizationMenu(MenuInterface $menu)
    {
        $nodes = [];

        if ($this->hasAnyPermissions('attribute')) {
            $attributeUrls = [];

            $attributeNode = $this
                                ->menuNodeFactory
                                ->create()
                                ->setName('admin.attribute.plural');

            if ($this->permissions['attribute']['canRead']) {
                $attributeNode->setUrl('admin_attribute_list');
            }
            elseif ($this->permissions['attribute']['canCreate']) {
                $attributeNode->setUrl('admin_attribute_new');
            }

            if ($this->permissions['attribute']['canCreate']) {
                $attributeUrls[] = 'admin_attribute_new';
            }

            if ($this->permissions['attribute']['canUpdate']) {
                $attributeUrls[] = 'admin_attribute_edit';
            }

            if (!empty($attributeUrls)) {
                $attributeNode->setActiveUrls($attributeUrls);
            }

            $nodes[] = $attributeNode;
        }

        if ($this->hasAnyPermissions('manufacturer')) {
            $manufacturerUrls = [];
            $manufacturerNode = $this
                                    ->menuNodeFactory
                                    ->create()
                                    ->setName('admin.manufacturer.plural');

            if ($this->permissions['manufacturer']['canRead']) {
                $manufacturerNode->setUrl('admin_manufacturer_list');
            }
            elseif ($this->permissions['manufacturer']['canCreate']) {
                $manufacturerNode->setUrl('admin_manufacturer_new');
            }

            if ($this->permissions['manufacturer']['canCreate']) {
                $manufacturerUrls[] = 'admin_manufacturer_new';
            }

            if ($this->permissions['manufacturer']['canUpdate']) {
                $manufacturerUrls[] = 'admin_manufacturer_edit';
            }

            if (!empty($manufacturerUrls)) {
                $manufacturerNode->setActiveUrls($manufacturerUrls);
            }

            $nodes[] = $manufacturerNode;
        }

        if ($this->hasAnyPermissions('category')) {
            $categoryUrls = [];
            $categoryNode = $this
                                ->menuNodeFactory
                                ->create()
                                ->setName('admin.category.plural');

            if ($this->permissions['category']['canRead']) {
                $categoryNode->setUrl('admin_category_list');
            }
            elseif ($this->permissions['category']['canCreate']) {
                $categoryNode->setUrl('admin_category_new');
            }

            if ($this->permissions['category']['canCreate']) {
                $categoryUrls[] = 'admin_category_new';
            }

            if ($this->permissions['category']['canUpdate']) {
                $categoryUrls[] = 'admin_category_edit';
            }

            if (!empty($categoryUrls)) {
                $categoryNode->setActiveUrls($categoryUrls);
            }

            $nodes[] = $categoryNode;
        }

        if (!empty($nodes)) {
            $rootNode = $this
                            ->menuNodeFactory
                            ->create()
                            ->setName('admin.categorization.single')
                            ->setCode('tag')
                            ->setTag('catalog')
                            ->setPriority(30);

            foreach ($nodes as $node) {
                $rootNode->addSubnode($node);
            }

            $menu->addSubnode($rootNode);
        }

        return $this;
    }

    private function setPermissions()
    {
        foreach ($this->resources as $resource) {
            $this->permissions[$resource] = [
                'canRead' => $this->permissionsRepository->canReadEntity($resource, $this->currentUser),
                'canCreate' => $this->permissionsRepository->canCreateEntity($resource, $this->currentUser),
                'canUpdate' => $this->permissionsRepository->canUpdateEntity($resource, $this->currentUser),
                'canDelete' => $this->permissionsRepository->canDeleteEntity($resource, $this->currentUser)
            ];
        }
    }

    private function hasAnyPermissions($resource)
    {
        foreach ($this->permissions[$resource] as $key => $value) {
            if ($value) {
                return true;
            }
        }

        return false;
    }
}
