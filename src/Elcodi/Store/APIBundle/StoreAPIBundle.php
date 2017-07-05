<?php

namespace Elcodi\Store\APIBundle;

use Elcodi\Bundle\CoreBundle\Abstracts\AbstractElcodiBundle;
use Elcodi\Store\APIBundle\DependencyInjection\StoreAPIExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Class StoreAPIBundle
 */
class StoreAPIBundle extends AbstractElcodiBundle
{
    /**
     * Returns the bundle's container extension.
     *
     * @return ExtensionInterface The container extension
     */
    public function getContainerExtension()
    {
        return new StoreAPIExtension();
    }
}
