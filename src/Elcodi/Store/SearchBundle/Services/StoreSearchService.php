<?php

namespace Elcodi\Store\SearchBundle\Services;

use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\MultiMatch;
use Elastica\Query\Terms;
use Elastica\Query\Term;
use Elastica\Query\Range;
use Elastica\Query\Nested;
use Elastica\Query\HasChild;
use Elcodi\Component\Currency\Services\CurrencyConverter;
use Elcodi\Component\Currency\Entity\Money;

use Elcodi\Store\SearchBundle\Services\IStoreSearchService;

/**
 * Defines all the search methods
 */
class StoreSearchService implements IStoreSearchService
{
    private $container;
    private $prefix;
    private $paginator;
    private $itemsPerPage;
    private $currencyConverter;
    private $currencyRepository;
    private $currentCurrency;
    private $defaultCurrency;
    private $categoryDefaultConnector;

    function __construct(
        \Symfony\Component\DependencyInjection\ContainerInterface $container, 
        $prefix, 
        $itemsPerPage, 
        CurrencyConverter $currencyConverter,
        $categoryDefaultConnector
    ) {
        $this->container = $container;
        $this->prefix = $prefix;
        $this->itemsPerPage = $itemsPerPage;
        $this->currencyConverter = $currencyConverter;
        $this->categoryDefaultConnector = $categoryDefaultConnector;

        $this->paginator = $this->container->get('knp_paginator');
        $this->currencyRepository = $this->container->get('elcodi.repository.currency');
        $this->currentCurrency = $this->container->get('elcodi.wrapper.currency')->get();
        $this->defaultCurrency = $this->container->get('elcodi.wrapper.default_currency')->get();
    }

    public function searchProducts($query, $page = 1, $limit = null, array $categories = array(), array $priceRange = array(), $categoryConnector = null)
    {
        if (empty($limit)) {
            $limit = $this->itemsPerPage;
        }

        if (empty($categoryConnector)) {
            $categoryConnector = $this->categoryDefaultConnector;
        }

        $finder = $this->createFinderFor('products');

        //$adapter = $finder->createPaginatorAdapter('*'.$query.'*');
        $productQuery = $this->createQueryForProducts($query, $categories, $priceRange, $categoryConnector);
        $adapter = $finder->createPaginatorAdapter($productQuery);

        return $this->paginator->paginate($adapter, $page, $limit);
    }

    private function createFinderFor($type)
    {
        return $this->container->get('fos_elastica.finder.app.'.$type);
    }

    private function createQueryForProducts($query, array $categories = array(), array $priceRange = array(), $categoryConnector)
    {
        $boolQuery = new BoolQuery();

        $enableQuery = new Term();
        $enableQuery->setTerm('enabled', true);
        $boolQuery->addFilter($enableQuery);

        if (!empty($query)) {
            $fieldsBoolQuery = new BoolQuery();

            $fieldQuery = new MultiMatch();
            $fieldQuery->setQuery($query);
            $fieldQuery->setFields([
                'name', 'sku', 'shortDescription', 'description' 
            ]);

            $fieldsBoolQuery->addShould($fieldQuery);
            $this->setNestedQueriesForProduct($fieldsBoolQuery, $query);

            $boolQuery->addMust($fieldsBoolQuery);
        }
        
        if (!empty($categories)) {
            $this->setCategoriesQuery($boolQuery, $categories, $categoryConnector);
        }

        if (!empty($priceRange)) {
            $this->setPriceRangeQuery($boolQuery, $priceRange);
        }

        return $boolQuery;
    }

    private function setNestedQueriesForProduct(BoolQuery $boolQuery, $query)
    {
        $categories = new Nested();
        $categories->setPath('categories');

        $categoriesQuery = new BoolQuery();
        $categoriesQuery->addShould(new Match('categories.name', $query));

        $categories->setQuery($categoriesQuery);

        $boolQuery->addShould($categories);

        $variants = new Nested();
        $variants->setPath('variants');
        $variantsQuery = new MultiMatch();
        $variantsQuery->setQuery($query);
        $variantsQuery->setFields([
           'variants.name', 'variants.sku', 'variants.shortDescription', 'variants.description' 
        ]);

        $variantsBool = new BoolQuery();
        $variantsBool->addShould($variantsQuery);
        $variants->setQuery($variantsBool);

        $boolQuery->addShould($variants);
    }

    private function setPriceRangeQuery(BoolQuery $boolQuery, array $priceRange)
    {
        $priceRange = array_map(function($item) {
            $item = floatval($item);
            $money = $this->currencyConverter->convertMoney(Money::create($item*100, $this->currentCurrency), $this->defaultCurrency);

            return $money->getAmount();
        }, $priceRange);

        $range = [
            'from' => $priceRange[0]
        ];

        if (count($priceRange) > 1) {
            $range['to'] = $priceRange[1];
        }

        $priceQuery = new Range('price.amount', $range);
        $boolQuery->addMust($priceQuery);
    }

    private function setCategoriesQuery(BoolQuery $boolQuery, array $categories, $categoryConnector)
    {
        $categoriesQuery = new Nested();
        $categoriesQuery->setPath('categories');
        
        $categoriesBool = new BoolQuery();
        if ($categoryConnector == 'or') {
            $categoriesBool->addMust(new Terms('categories.id', $categories));
        }
        elseif ($categoryConnector == 'and') {
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
