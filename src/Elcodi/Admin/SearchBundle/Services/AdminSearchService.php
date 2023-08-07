<?php

namespace Elcodi\Admin\SearchBundle\Services;

use DateTime;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\MultiMatch;
use Elastica\Query\Nested;
use Elastica\Query\QueryString;
use Elastica\Query\Range;
use Elastica\Query\Wildcard;
use Elastica\Util;
use Elcodi\Admin\SearchBundle\Services\IAdminSearchService;

/**
 * Defines all the search methods
 */
class AdminSearchService implements IAdminSearchService
{
    protected $container;
    protected $prefix;
    protected $itemsPerPage;
    protected $paginator;

    protected $limit;
    private $searchProductsConnector;
    private $searchProductsWithVariants;

    public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container, $prefix, $itemsPerPage, $searchProductsConnector, $searchProductsWithVariants)
    {
        $this->container = $container;
        $this->prefix = $prefix;
        $this->itemsPerPage = $itemsPerPage;
        $this->limit = $this->itemsPerPage;

        $this->paginator = $this->container->get('knp_paginator');
        $this->searchProductsConnector = $searchProductsConnector;
        $this->searchProductsWithVariants = $searchProductsWithVariants;
    }

    public function searchProducts($query, $page = 1, $limit = null)
    {
        $finder = $this->createFinderFor('products');

        if (empty($limit)) {
            $limit = $this->itemsPerPage;
        }
        $this->limit = $limit;

        $productQuery = $this->createQueryForProducts($query);

        //Per vedere la query che esegue ELASTICSEARCH decommenta sotto.
        // $json = json_encode($productQuery->toArray());
        // echo $json;
        $adapter = $finder->createPaginatorAdapter($productQuery);
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

        if (!empty($query)) {
            if (strpos($query, '@') !== false) {
                $wildcardBool = $this->createWildcardQuery($query);
                $boolQuery->addShould($wildcardBool);
            } else {
                $queryString = $this->createQueryString($query);
                $boolQuery->addShould($queryString);
            }
        }
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

        $boolQuery = new BoolQuery();

        if (!empty($query)) {
            $boolQuery->addShould(new QueryString('*' . $query . '*'));
        }

//		$adapter = $finder->createPaginatorAdapter('*' . Util::escapeTerm($query) . '*');
        $adapter = $finder->createPaginatorAdapter($boolQuery);
        return $this->paginator->paginate($adapter, $page, $limit);
    }

    public function getLimit()
    {
        return $this->limit;
    }

    protected function createFinderFor($type)
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

    private function createWildcardQuery($query)
    {
        $wildcardBool = new BoolQuery();

        $wildcardBool->addShould(new Wildcard('email', '*' . $query . '*'));

        return $wildcardBool;
    }

    private function createQueryString($query)
    {
        $queryString = new BoolQuery();
        if (!empty($query)) {
            $queryString->addShould(new QueryString($query));
        }

        return $queryString;
    }

    protected function createQueryForProducts($query)
    {
        $boolQuery = new BoolQuery();

        if (!empty($query)) {
            $query = trim($query);
            $query = strtolower($query);
            $baseQuery = new BoolQuery();

            $productsPartialQuery = $this->setQueryForPartialProducts($query);
            $baseQuery->addShould($productsPartialQuery);

            $productsQuery = $this->setQueryForProducts($query);
            $baseQuery->addShould($productsQuery);

            if ($this->searchProductsWithVariants) {
                $variantsQuery = $this->setNestedQueriesForVariants($query);
                $baseQuery->addShould($variantsQuery);
            }
            $boolQuery->addMust($baseQuery);
        }

        $finalQuery = new Query($boolQuery);

        return $finalQuery;
    }

    /**
     * Query per la ricerca parziale dei prodotti, cercado in name shortDescription, description, sku
     * @param string $query stringa da cercare
     */
    protected function setQueryForPartialProducts($query)
    {
        $wildcardBool = new BoolQuery();

        $wildcardBool->addShould(new Wildcard('name', '*' . $query . '*'));
        $wildcardBool->addShould(new Wildcard('shortDescription', '*' . $query . '*'));
        $wildcardBool->addShould(new Wildcard('description', '*' . $query . '*'));
        $wildcardBool->addShould(new Wildcard('sku', '*' . $query . '*'));

        return $wildcardBool;
    }

    protected function setQueryForProducts($query)
    {
        $totalProductsQuery = new BoolQuery();
        $tokens = explode(' ', $query);
        foreach ($tokens as $token) {
            $token = trim($token);

            $productsQuery = new MultiMatch();
            $productsQuery->setQuery($token);
            $productsQuery->setFields([
                'name', 'shortDescription', 'description', 'sku',
            ]);
            if ($this->searchProductsConnector == 'or') {
                $totalProductsQuery->addShould($productsQuery);
            } else {
                $totalProductsQuery->addMust($productsQuery);
            }
        }
        return $totalProductsQuery;
    }

    protected function setNestedQueriesForVariants($query)
    {
        $totalVariantsQuery = new BoolQuery();
        $tokens = explode(' ', $query);
        foreach ($tokens as $token) {
            $token = trim($token);

            $variants = new Nested();
            $variants->setPath('variants');
            $variantsQuery = new MultiMatch();
            $variantsQuery->setQuery($token);
            $variantsQuery->setFields([
                'variants.name', 'variants.shortDescription', 'variants.description', 'variants.sku',
            ]);

            if ($this->searchProductsConnector == 'or') {
                $totalVariantsQuery->addShould($variantsQuery);
            } else {
                $totalVariantsQuery->addMust($variantsQuery);
            }
        }
        return $totalVariantsQuery;
    }
}
