<?php

namespace Elcodi\Admin\SearchBundle\Services;

use Elcodi\Admin\SearchBundle\Services\IAdminSearchService;

/**
 * Defines all the search methods
 */
class AdminSearchService implements IAdminSearchService
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
        return $finder->find($query);
    }

    public function searchCustomers($query)
    {
        $finder = $this->createFinderFor('customers');
        return $finder->find($query);
    }

    public function searchManufacturers($query)
    {
        $finder = $this->createFinderFor('manufacturers');
        return $finder->find($query);
    }

    private function createFinderFor($type)
    {
        return $this->container->get('fos_elastica.finder.app.'.$type);
    }
}
