<?php

namespace Elcodi\Admin\SearchBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use Elcodi\Bundle\CoreBundle\Abstracts\AbstractElcodiBundle;
use Elcodi\Admin\SearchBundle\DependencyInjection\AdminSearchExtension;

/**
 * Class StoreSearchBundle
 */
class AdminSearchBundle extends AbstractElcodiBundle
{
    /**
     * Returns the bundle's container extension.
     *
     * @return ExtensionInterface The container extension
     */
    public function getContainerExtension()
    {
        return new AdminSearchExtension();
    }
}
