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

namespace Elcodi\Admin\PageBundle\Builder;

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
    private $resource = "page";
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
            $pageActiveUrls = [];
            $blogActiveUrls = [];
            $emailActiveUrls = [];
            
            $pageNode = $this->menuNodeFactory
                                ->create()
                                ->setName('admin.page.plural')
                                ->setCode('file-text-o');
            
            $blogNode = $this->menuNodeFactory
                                ->create()
                                ->setName('admin.blog.single')
                                ->setCode('pencil');

            $emailNode = $this->menuNodeFactory
                                ->create()
                                ->setName('admin.mailing.plural')
                                ->setCode('envelope-o');

            if ($this->permisssions['canRead']) {
                $emailActiveUrls[] = 'admin_email_list';

                $pageNode->setUrl('admin_page_list');
                $blogNode->setUrl('admin_blog_post_list');
                $emailNode->setUrl('admin_email_list');
            }
            elseif ($this->permissions['canCreate']) {
                $pageNode->setUrl('admin_page_new');
                $blogNode->setUrl('admin_blog_post_new');
            }

            if ($this->permissions['canCreate']) {
                $pageActiveUrls[] = 'admin_page_new';
                $blogActiveUrls[] = 'admin_blog_post_new';
            }

            if ($this->permissions['canUpdate']) {
                $pageActiveUrls[] = 'admin_page_edit';
                $blogActiveUrls[] = 'admin_blog_post_edit';
                $emailActiveUrls[] = 'admin_email_edit';
            }

            if (!empty($pageActiveUrls)) {
                $pageNode->setActiveUrls($pageActiveUrls);
            }

            if (!empty($blogActiveUrls)) {
                $blogNode->setActiveUrls($blogActiveUrls);
            }

            if (!empty($emailActiveUrls)) {
                $emailNode->setActiveUrls($emailActiveUrls);
            }

            $menu
                ->findSubnodeByName('admin.communication.single')
                ->addSubnode($pageNode)
                ->addSubnode($blogNode)
                ->addSubnode($emailNode);
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
