<?php

namespace Elcodi\Admin\PermissionsBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use Elcodi\Admin\PermissionsBundle\DependencyInjection\AdminPermissionsExtension;
use Elcodi\Bundle\CoreBundle\Abstracts\AbstractElcodiBundle;

class AdminPermissionsBundle extends AbstractElcodiBundle
{
    /**
     * Returns the bundle's container extension.
     *
     * @return ExtensionInterface The container extension
     */
    public function getContainerExtension()
    {
        return new AdminPermissionsExtension();
    }
}