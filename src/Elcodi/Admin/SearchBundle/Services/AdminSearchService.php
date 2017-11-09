<?php

namespace Elcodi\Admin\SearchBundle\Services;

use DateTime;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\MultiMatch;
use Elastica\Query\Wildcard;
use Elastica\Query\Nested;
use Elastica\Query\Range;
use Elastica\Util;
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

    public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container, $prefix, $itemsPerPage)
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

        $adapter = $finder->createPaginatorAdapter('*' . $query . '*');
        return $this->paginator->paginate($adapter, $page, $limit);
    }

    public function searchOrders($query, $page = 1, $limit = null, array $dateRange = array())
    {
        $finder = $this->createFinderFor('orders');

        if (empty($limit)) {
            $limit = $this->itemsPerPage;
        }
        $this->limit = $limit;

        $orderQuery = $this->createQueryForOrder($query, $dateRange);

        $adapter = $finder->createPaginatorAdapter($orderQuery);

        //$adapter = $finder->createPaginatorAdapter('*'.$query.'*');
        return $this->paginator->paginate($adapter, $page, $limit);
    }

    public function searchCustomers($query, $page = 1, $limit = null)
    {
        $finder = $this->createFinderFor('customers');

        if (empty($limit)) {
            $limit = $this->itemsPerPage;
        }
        $this->limit = $limit;

        $boolQuery = new BoolQuery();

        $fieldQuery = $this->createMultiMatchQuery($query);
        $boolQuery->addShould($fieldQuery);

        $wildcardBool = $this->createWildcardQuery($query);
        $boolQuery->addShould($wildcardBool);

        $adapter = $finder->createPaginatorAdapter($boolQuery);

        $options = array();
        $options['defaultSortFieldName'] = 'id';
        $options['defaultSortDirection'] = 'desc';
        return $this->paginator->paginate($adapter, $page, $limit, $options);
    }

    public function searchManufacturers($query, $page = 1, $limit = null)
    {
        $finder = $this->createFinderFor('manufacturers');

        if (empty($limit)) {
            $limit = $this->itemsPerPage;
        }
        $this->limit = $limit;

        $adapter = $finder->createPaginatorAdapter('*' . $query . '*');
        return $this->paginator->paginate($adapter, $page, $limit);
    }

    public function searchCoupons($query, $page = 1, $limit = null)
    {
        $finder = $this->createFinderFor('coupons');

        if (empty($limit)) {
            $limit = $this->itemsPerPage;
        }
        $this->limit = $limit;

        $adapter = $finder->createPaginatorAdapter('*' . Util::escapeTerm($query) . '*');
        return $this->paginator->paginate($adapter, $page, $limit);
    }

    public function getLimit()
    {
        return $this->limit;
    }

    private function createFinderFor($type)
    {
        return $this->container->get('fos_elastica.finder.app.' . $type);
    }

    private function createQueryForOrder($query, array $dateRange = array())
    {
        $boolQuery = new BoolQuery();

        if (!empty($query)) {
            // creo la query per la stringa
            $fieldsBoolQuery = new BoolQuery();

            $fieldQuery = new MultiMatch();
            $fieldQuery->setQuery($query);
            $fieldQuery->setFields([
                'customer.email', 'customer.firstName', 'customer.lastName',
            ]);

            $fieldsBoolQuery->addShould($fieldQuery);
            $this->setNestedQueryForOrder($fieldsBoolQuery, $query);

            $boolQuery->addMust($fieldsBoolQuery);
        }

        if (!empty($dateRange)) {
            $this->setDateRangeQuery($boolQuery, $dateRange);
        }

        return $boolQuery;
    }

    private function setNestedQueryForOrder(BoolQuery $boolQuery, $query)
    {
        $orderItems = new Nested();
        $orderItems->setPath('orderLines');

        $products = new BoolQuery();

        $fieldsBoolQuery = new BoolQuery();
        $fieldQuery = new MultiMatch();
        $fieldQuery->setQuery($query);
        $fieldQuery->setFields([
            'orderLines.purchasable.name', 'orderLines.purchasable.sku', 'orderLines.purchasable.shortDescription', 'orderLines.purchasable.description',
        ]);

        $fieldsBoolQuery->addShould($fieldQuery);

        $categories = new Nested();
        $categories->setPath('orderLines.purchasable.categories');

        $categoriesQuery = new BoolQuery();
        $categoriesQuery->addShould(new Match('orderLines.purchasable.categories.name', $query));

        $categories->setQuery($categoriesQuery);

        $fieldsBoolQuery->addShould($categories);

        $products->addMust($fieldsBoolQuery);

        $orderItems->setQuery($products);
        $boolQuery->addShould($orderItems);
    }

    private function setDateRangeQuery(BoolQuery $boolQuery, array $dateRange)
    {
        $range = [];
        if (!empty($dateRange['from'])) {
            $range['gte'] = DateTime::createFromFormat('Y-m-d', $dateRange['from'])->format('Y-m-d 00:00:00');
        }

        if (!empty($dateRange['to'])) {
            $range['lte'] = DateTime::createFromFormat('Y-m-d', $dateRange['to'])->format('Y-m-d 23:59:59');
        }

        $range['format'] = 'yyyy-MM-dd HH:mm:ss';

        $dateQuery = new Range('createdAt', $range);
        $boolQuery->addMust($dateQuery);
    }

    private function createMultiMatchQuery($query)
    {
        $fieldQuery = new MultiMatch();
        $fieldQuery->setQuery($query);
        $fieldQuery->setFields([
            'email', 'firstName', 'lastName',
        ]);

        return $fieldQuery;
    }

    private function createWildcardQuery($query)
    {
        $wildcardBool = new BoolQuery();
        $wildcardBool->addShould(new Wildcard('email', '*'.$query.'*'));
        $wildcardBool->addShould(new Wildcard('firstName', '*'.$query.'*'));
        $wildcardBool->addShould(new Wildcard('lastName', '*'.$query.'*'));

        return $wildcardBool;
    }
}
