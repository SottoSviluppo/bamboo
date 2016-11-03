<?php

namespace Elcodi\Admin\PermissionsBundle\Builder;

use Elcodi\Component\Menu\Builder\Abstracts\AbstractMenuBuilder;
use Elcodi\Component\Menu\Builder\Interfaces\MenuBuilderInterface;
use Elcodi\Component\Menu\Entity\Menu\Interfaces\MenuInterface;

class MenuBuilder extends AbstractMenuBuilder implements MenuBuilderInterface
{
    /**
     * Build the menu
     *
     * @param MenuInterface $menu Menu
     */
    public function build(MenuInterface $menu)
    {
        $menu
            ->addSubnode(
                $this
                    ->menuNodeFactory
                    ->create()
                    ->setName('Permissions')
                    ->setCode('key')
                    ->setUrl('admin_permissions_list')
                    ->setTag('settings')
                    ->setPriority(32)
                    /*->setActiveUrls([
                        'admin_permissions_edit',
                        'admin_permissions_new',
                    ])*/
            );
    }
}