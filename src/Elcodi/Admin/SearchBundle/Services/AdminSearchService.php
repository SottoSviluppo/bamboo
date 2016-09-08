<?php

namespace Elcodi\Admin\SearchBundle\Services;

use Elcodi\Admin\SearchBundle\Services\IAdminSearchService;

/**
 * Defines all the search methods
 */
class AdminSearchService implements IAdminSearchService
{
    private $container;
    private $prefix;
    private $itemsPerPage;
    private $paginator;

    private $limit;

    function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container, $prefix, $itemsPerPage)
    {
        $this->container = $container;
        $this->prefix = $prefix;
        $this->itemsPerPage = $itemsPerPage;
        $this->limit = $this->itemsPerPage;

        $this->paginator = $this->container->get('knp_paginator');
    }

    public function searchProducts($query, $page = 1, $limit = null)
    {
        $finder = $this->createFinderFor('products');

        if (empty($limit)) {
            $limit = $this->itemsPerPage;
        }
        $this->limit = $limit;

        $adapter = $finder->createPaginatorAdapter($query);
        return $this->paginator->paginate($adapter, $page, $limit);
    }

    public function searchOrders($query, $page = 1, $limit = null)
    {
        $finder = $this->createFinderFor('orders');
        
        if (empty($limit)) {
            $limit = $this->itemsPerPage;
        }
        $this->limit = $limit;

        $adapter = $finder->createPaginatorAdapter($query);
        return $this->paginator->paginate($adapter, $page, $limit);
    }

    public function searchCustomers($query, $page = 1, $limit = null)
    {
        $finder = $this->createFinderFor('customers');
        
        if (empty($limit)) {
            $limit = $this->itemsPerPage;
        }
        $this->limit = $limit;

        $adapter = $finder->createPaginatorAdapter($query);
        return $this->paginator->paginate($adapter, $page, $limit);
    }

    public function searchManufacturers($query, $page = 1, $limit = null)
    {
        $finder = $this->createFinderFor('manufacturers');
        
        if (empty($limit)) {
            $limit = $this->itemsPerPage;
        }
        $this->limit = $limit;

        $adapter = $finder->createPaginatorAdapter($query);
        return $this->paginator->paginate($adapter, $page, $limit);
    }

    public function getLimit()
    {
        return $this->limit;
    }

    private function createFinderFor($type)
    {
        return $this->container->get('fos_elastica.finder.app.'.$type);
    }
}
