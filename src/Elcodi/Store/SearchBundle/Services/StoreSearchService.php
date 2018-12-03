<?php

namespace Elcodi\Store\SearchBundle\Services;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Filtered;
use Elastica\Query\Match;
use Elastica\Query\MultiMatch;
use Elastica\Query\Nested;
use Elastica\Query\QueryString;
use Elastica\Query\Range;
use Elastica\Query\Term;
use Elastica\Query\Terms;
use Elastica\Query\Wildcard;
use Elcodi\Component\Currency\Entity\Money;
use Elcodi\Component\Currency\Services\CurrencyConverter;
use Elcodi\Store\SearchBundle\Services\IStoreSearchService;

/**
 * Defines all the search methods
 */
class StoreSearchService implements IStoreSearchService
{
    protected $container;
    protected $prefix;
    protected $paginator;
    protected $itemsPerPage;
    protected $currencyConverter;
    protected $currencyRepository;
    protected $currentCurrency;
    protected $defaultCurrency;
    protected $categoryDefaultConnector;
    protected $productPartialSearch;
    protected $categoryPartialSearch;
    protected $searchProductsConnector;
    protected $searchProductsWithVariants;

    protected $sortArray = array(
        'principalCategory.name' => array('order' => 'asc'),
        'id' => array('order' => 'asc'),
    );

    public function setSortArray($sortArray)
    {
        $this->sortArray = $sortArray;
    }

    public function __construct(
        \Symfony\Component\DependencyInjection\ContainerInterface $container,
        $prefix,
        $itemsPerPage,
        CurrencyConverter $currencyConverter,
        $categoryDefaultConnector,
        $productPartialSearch,
        $categoryPartialSearch,
        $searchProductsConnector,
        $searchProductsWithVariants
    ) {
        $this->container = $container;
        $this->prefix = $prefix;
        $this->itemsPerPage = $itemsPerPage;
        $this->currencyConverter = $currencyConverter;
        $this->categoryDefaultConnector = $categoryDefaultConnector;
        $this->productPartialSearch = $productPartialSearch;
        $this->categoryPartialSearch = $categoryPartialSearch;

        $this->paginator = $this->container->get('knp_paginator');
        $this->currencyRepository = $this->container->get('elcodi.repository.currency');
        $this->currentCurrency = $this->container->get('elcodi.wrapper.currency')->get();
        $this->defaultCurrency = $this->container->get('elcodi.wrapper.default_currency')->get();
        $this->searchProductsConnector = $searchProductsConnector;
        $this->searchProductsWithVariants = $searchProductsWithVariants;
    }

    public function searchProducts($query, $page = 1, $limit = null, array $categories = array(), array $priceRange = array(), $categoryConnector = null)
    {
        if ($query == '') {
            $query = '_';
        }

        if (empty($limit)) {
            $limit = $this->itemsPerPage;
        }

        if (empty($categoryConnector)) {
            $categoryConnector = $this->categoryDefaultConnector;
        }

        $finder = $this->createFinderFor('products');

        //$adapter = $finder->createPaginatorAdapter('*'.$query.'*');
        $productQuery = $this->createQueryForProducts($query, $categories, $priceRange, $categoryConnector);
        // $boolQuery->setSort(array('id' => array('order' => 'asc')));

        $adapter = $finder->createPaginatorAdapter($productQuery);
        return $this->paginator->paginate($adapter, $page, $limit);
    }

    protected function createFinderFor($type)
    {
        return $this->container->get('fos_elastica.finder.app.' . $type);
    }

    protected function createQueryForProducts($query, array $categories = array(), array $priceRange = array(), $categoryConnector)
    {
        $boolQuery = $this->getBoolQueryForProducts($query, $categories, $priceRange, $categoryConnector);

        // ordina i risultati della ricerca per principalCategory e per id del prodotto
        $finalQuery = new Query($boolQuery);

        if (empty($query) and !empty($categories)) {
            $finalQuery->setSort($this->sortArray);
        }

        return $finalQuery;
    }

    protected function getBoolQueryForProducts($query, array $categories = array(), array $priceRange = array(), $categoryConnector)
    {
        $boolQuery = new BoolQuery();

        $enableQuery = new Term();
        $enableQuery->setTerm('enabled', true);
        $enableQuery->setTerm('private', false);
        $boolQuery->addFilter($enableQuery);

        if (!empty($query)) {
            $query = trim($query);
            $baseQuery = new BoolQuery();

            $productsQuery = $this->setQueryForProducts($query);
            $baseQuery->addShould($productsQuery);

            if ($this->searchProductsWithVariants) {
                $variantsQuery = $this->setNestedQueriesForVariants($query);
                $baseQuery->addShould($variantsQuery);
            }
            $boolQuery->addMust($baseQuery);
        }

        //debugging
        // echo "<pre>";
        // print_r($boolQuery->toArray());die();

        if (!empty($categories)) {
            $this->setCategoriesQuery($boolQuery, $categories, $categoryConnector);
        }

        if (!empty($priceRange)) {
            $this->setPriceRangeQuery($boolQuery, $priceRange);
        }
        return $boolQuery;
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

    /*protected function setNestedQueriesForProduct(BoolQuery $boolQuery, $query)
    {
    $categories = new Nested();
    $categories->setPath('categories');

    $categoriesQuery = new BoolQuery();

    $categoryNameQuery = new Wildcard('categories.name', '*' . $query . '*');
    if (!$this->categoryPartialSearch) {
    $categoryNameQuery = new Match('categories.name', $query);
    }
    $categoriesQuery->addShould($categoryNameQuery);

    $categories->setQuery($categoriesQuery);

    $boolQuery->addShould($categories);

    $variants = new Nested();
    $variants->setPath('variants');
    $variantsQuery = new MultiMatch();
    $variantsQuery->setQuery($query);
    $variantsQuery->setFields([
    'variants.shortDescription', 'variants.description',
    ]);

    $variantsBool = new BoolQuery();
    $variantsBool->addShould($variantsQuery);

    $variantNameQuery = new Match('variants.name', $query);
    if ($this->productPartialSearch) {
    $variantNameQuery = new Wildcard('variants.name', '*' . $query . '*');
    }
    $variantsBool->addShould($variantNameQuery);

    $variantsSkuQuery = new Match('variants.sku', $query);
    if ($this->productPartialSearch) {
    $variantsSkuQuery = new Wildcard('variants.sku', '*' . $query . '*');
    }
    $variantsBool->addShould($variantsSkuQuery);

    $variants->setQuery($variantsBool);

    $boolQuery->addShould($variants);

    $manufacturers = new BoolQuery();
    $manufacturerNameQuery = new Wildcard('manufacturer.name', '*' . $query . '*');
    if (!$this->categoryPartialSearch) {
    $manufacturerNameQuery = new Match('manufacturer.name', $query);
    }
    $manufacturers->addShould($manufacturerNameQuery);
    $boolQuery->addShould($manufacturers);
    }*/

    protected function setPriceRangeQuery(BoolQuery $boolQuery, array $priceRange)
    {
        $priceRange = array_map(function ($item) {
            $item = floatval($item);
            $money = $this->currencyConverter->convertMoney(Money::create($item * $this->currentCurrency->getDivideBy(), $this->currentCurrency), $this->defaultCurrency);

            return $money->getAmount();
        }, $priceRange);

        $range = [
            'from' => $priceRange[0],
        ];

        if (count($priceRange) > 1) {
            $range['to'] = $priceRange[1];
        }

        $priceQuery = new Range('price.amount', $range);
        $boolQuery->addMust($priceQuery);
    }

    protected function setCategoriesQuery(BoolQuery $boolQuery, array $categories, $categoryConnector)
    {
        $categoriesQuery = new Nested();
        $categoriesQuery->setPath('categories');

        $categoriesBool = new BoolQuery();
        if ($categoryConnector == 'or') {
            $categoriesBool->addMust(new Terms('categories.id', $categories));
        } elseif ($categoryConnector == 'and') {
            foreach ($categories as $category) {
                $categoryIdNested = new Nested();
                $categoryIdNested->setPath('categories');

                $categoryIdBool = new BoolQuery();

                $categoryId = new Term();
                $categoryId->setTerm('categories.id', $category);
                $categoryIdBool->addMust($categoryId);

                $categoryIdNested->setQuery($categoryIdBool);
                $boolQuery->addMust($categoryIdNested);
            }
        }

        $enableQuery = new Term();
        $enableQuery->setTerm('categories.enabled', true);
        $categoriesBool->addFilter($enableQuery);

        $categoriesQuery->setQuery($categoriesBool);

        $boolQuery->addMust($categoriesQuery);
    }
}
