<?php

namespace Elcodi\Store\SearchBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use Elcodi\Bundle\CoreBundle\Abstracts\AbstractElcodiBundle;
use Elcodi\Store\SearchBundle\DependencyInjection\StoreSearchExtension;

/**
 * Class StoreSearchBundle
 */
class StoreSearchBundle extends AbstractElcodiBundle
{
    /**
     * Returns the bundle's container extension.
     *
     * @return ExtensionInterface The container extension
     */
    public function getContainerExtension()
    {
        return new StoreSearchExtension();
    }
}
