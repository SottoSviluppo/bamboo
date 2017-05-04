<?php

namespace Elcodi\Admin\SearchBundle\Services;

use DateTime;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\MultiMatch;
use Elastica\Query\Nested;
use Elastica\Query\Range;
use Elcodi\Admin\SearchBundle\Services\IAdminSearchService;

class AdminOrderSearchService
{
    private $container;
    private $prefix;
    private $itemsPerPage;
    private $paginator;

    private $page;
    private $limit;

    private $orderQuery;

    public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container, $prefix, $itemsPerPage)
    {
        $this->container = $container;
        $this->prefix = $prefix;
        $this->itemsPerPage = $itemsPerPage;

        $this->page = 1;
        $this->limit = $this->itemsPerPage;

        $this->paginator = $this->container->get('knp_paginator');
        $this->orderQuery = new BoolQuery();

    }

    public function getOrderQuery()
    {
        return $this->orderQuery;
    }

    public function printDebug()
    {
        echo "<pre>";
        print_r($this->orderQuery->toArray());
        echo "</pre>";
    }

    public function searchOrders($query, $page = 1, $limit = null, array $dateRange = array())
    {
        $this->createQueryForOrder($query, $dateRange);
        return $this->getPaginator();
    }

    public function getPaginator()
    {
        $finder = $this->createFinderFor('orders');
        $adapter = $finder->createPaginatorAdapter($this->orderQuery);
        if (empty($limit)) {
            $limit = $this->itemsPerPage;
        }
        $this->limit = $limit;

        $options = [];
        $options['defaultSortFieldName'] = 'paymentLastStateLine.id';
        $options['defaultSortDirection'] = 'DESC';

        return $this->paginator->paginate($adapter, $this->page, $limit, $options);
    }

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    private function createFinderFor($type)
    {
        return $this->container->get('fos_elastica.finder.app.' . $type);
    }

    public function addQuery($query)
    {
        if (empty($query)) {
            return;
        }

        // creo la query per la stringa
        $fieldsBoolQuery = new BoolQuery();

        $fieldQuery = new MultiMatch();
        $fieldQuery->setQuery($query);
        $fieldQuery->setFields([
            'customer.email', 'customer.firstName', 'customer.lastName',
        ]);

        $fieldsBoolQuery->addShould($fieldQuery);
        $this->setNestedQueryForOrder($fieldsBoolQuery, $query);

        $this->orderQuery->addMust($fieldsBoolQuery);
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

    public function addDateRange(array $dateRange)
    {
        if (empty($dateRange)) {
            return;
        }

        $range = [];
        if (!empty($dateRange['from'])) {
            $range['gte'] = DateTime::createFromFormat('Y-m-d', $dateRange['from'])->format('Y-m-d 00:00:00');
        }

        if (!empty($dateRange['to'])) {
            $range['lte'] = DateTime::createFromFormat('Y-m-d', $dateRange['to'])->format('Y-m-d 23:59:59');
        }

        $range['format'] = 'yyyy-MM-dd HH:mm:ss';

        $dateQuery = new Range('createdAt', $range);
        $this->orderQuery->addMust($dateQuery);
    }

    public function addCustomerEmail($query)
    {
        return $this->addMatch('customer.email', $query);
    }

    public function addOrderPaymentState($orderState)
    {
        return $this->addExactMatch('paymentLastStateLine.name', $orderState);
    }

    public function addOrderShippingState($orderState)
    {
        return $this->addExactMatch('shippingLastStateLine.name', $orderState);
    }

    public function addOrderPaymentMethod($paymentMethod)
    {
        return $this->addExactMatch('paymentMethod.name', $paymentMethod);
    }

    public function addCountry($countryId)
    {
        return $this->addMatch('addressDelivery.country.id', $countryId);
    }

    public function addMatch($name, $value)
    {
        if (empty($value)) {
            return;
        }

        $stateQuery = new Match();
        $stateQuery->setFieldQuery($name, $value);
        $this->orderQuery->addMust($stateQuery);
    }

    public function addExactMatch($name, $value)
    {
        if (empty($value)) {
            return;
        }

        $stateQuery = new BoolQuery();
        $stateQuery->addShould(new Match($name, $value));
        $this->orderQuery->addMust($stateQuery);
    }

    public function addIdRange(array $idRange)
    {
        if (empty($idRange)) {
            return;
        }

        $range = [];
        if (!empty($idRange['from'])) {
            $range['gte'] = $idRange['from'];
        }

        if (!empty($idRange['to'])) {
            $range['lte'] = $idRange['to'];
        }

        $dateQuery = new Range('id', $range);
        $this->orderQuery->addMust($dateQuery);
    }
}
