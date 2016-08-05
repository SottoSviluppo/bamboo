<?php

namespace Elcodi\Store\SearchBundle\Services;

use Elcodi\Store\SearchBundle\Services\IStoreSearchService;

/**
 * Defines all the search methods
 */
class StoreSearchService implements IStoreSearchService
{
    private $container;

    function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function searchProducts($query)
    {
        $finder = $this->createFinderFor('products');
        return $finder->find($query);
    }

    public function searchOrders($query)
    {
        $finder = $this->createFinderFor('orders');
    }

    public function searchCustomers($query)
    {
        $finder = $this->createFinderFor('customers');
    }

    private function createFinderFor($type)
    {
        return $this->container->get('fos_elastica.finder.app.'.$type);
    }
}
