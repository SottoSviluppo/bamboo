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

    public function getResult()
    {
        // add sort to query
        $q = new \Elastica\Query($this->orderQuery);
        $sortParam = ['paymentLastStateLine.id' => ['order' => 'desc']];
        $q->setSort(array($sortParam))
            ->setMinScore(1);

        $finder = $this->createFinderFor('orders');
        return $finder->find($q, $this->limit);
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

    public function addCouponCampaign($couponCampaign)
    {
        if ($couponCampaign == '') {
            return;
        }

        $couponCampaignQuery = new Nested();
        $couponCampaignQuery->setPath('orderCoupons');

        $couponCampaignTerm = new Match();
        $couponCampaignTerm->setFieldQuery('orderCoupons.coupon.couponCampaign.campaignName', $couponCampaign);

        $couponCampaignQuery->setQuery($couponCampaignTerm);

        $this->orderQuery->addMust($couponCampaignQuery);
    }

    public function addCoupon($coupon)
    {
        if ($coupon == '') {
            return;
        }

        $couponQuery = new Nested();
        $couponQuery->setPath('orderCoupons');

        $couponTerm = new Match();
        $couponTerm->setFieldQuery('orderCoupons.coupon.code', $coupon);

        $couponQuery->setQuery($couponTerm);

        $this->orderQuery->addMust($couponQuery);
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
        return $this->addMatch('deliveryAddress.country.id', $countryId);
    }

    public function addExtraDataMatch($key, $value)
    {
        return $this->addMatch('extraData.' . $key, $value);
    }

    public function getRange($dateFrom, $dateTo)
    {
        $dateRange = [];
        if (!empty($dateFrom)) {
            $dateRange['from'] = $dateFrom;
        }

        if (!empty($dateTo)) {
            $dateRange['to'] = $dateTo;
        }
        return $dateRange;

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

    public function getSearchParameters($request)
    {
        $orderState = $request->get('orderState');
        $coupon = $request->get('coupon');
        $couponCampaign = $request->get('couponCampaign');
        $shippingState = $request->get('shippingState');
        $customerEmail = $request->get('customerEmail');
        $paymentMethod = $request->get('paymentMethod');
        $dateFrom = $request->get('dateFrom');
        $dateTo = $request->get('dateTo');
        $idFrom = $request->get('idFrom');
        $idTo = $request->get('idTo');
        $countryId = $request->get('countryId', 0);
        $template = $request->get('template', 'AdminCartBundle:Order:listComponent.html.twig');

        $dateRange = $this->getRange($dateFrom, $dateTo);
        $idRange = $this->getRange($idFrom, $idTo);

        return [
            'dateRange' => $dateRange,
            'orderState' => $orderState,
            'coupon' => $coupon,
            'couponCampaign' => $couponCampaign,
            'shippingState' => $shippingState,
            'countryId' => $countryId,
            'customerEmail' => $customerEmail,
            'paymentMethod' => $paymentMethod,
            'template' => $template,
            'idRange' => $idRange,
        ];
    }

    public function getOrdersPaginator($searchParameters)
    {
        $this->prepareSearchService($searchParameters);
        $ordersPaginator = $this->getPaginator();

        return $ordersPaginator;
    }

    public function prepareSearchService($searchParameters)
    {
        if ($searchParameters['query'] === "_") {
            $searchParameters['query'] = null;
        }

        if (array_key_exists('page', $searchParameters)) {
            $this->setPage($searchParameters['page']);
        }
        if (array_key_exists('limit', $searchParameters)) {
            $this->setLimit($searchParameters['limit']);
        }
        $this->addQuery($searchParameters['query']);
        $this->addDateRange($searchParameters['dateRange']);
        $this->addOrderPaymentState($searchParameters['orderState']);
        $this->addCoupon($searchParameters['coupon']);
        $this->addCouponCampaign($searchParameters['couponCampaign']);
        $this->addOrderShippingState($searchParameters['shippingState']);
        $this->addIdRange($searchParameters['idRange']);
        $this->addCustomerEmail($searchParameters['customerEmail']);
        $this->addOrderPaymentMethod($searchParameters['paymentMethod']);
        $this->addCountry($searchParameters['countryId']);
        return $this;
    }
}
