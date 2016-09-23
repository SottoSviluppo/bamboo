<?php

namespace Elcodi\Store\SearchBundle\Services;

use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\MultiMatch;
use Elastica\Query\Terms;
use Elastica\Query\Term;
use Elastica\Query\Range;
use Elastica\Query\Nested;

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

    function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container, $prefix, $itemsPerPage)
    {
        $this->container = $container;
        $this->prefix = $prefix;
        $this->itemsPerPage = $itemsPerPage;

        $this->paginator = $this->container->get('knp_paginator');
    }

    public function searchProducts($query, $page = 1, $limit = null, $categories = array(), $priceRange = array())
    {
        if (empty($limit)) {
            $limit = $this->itemsPerPage;
        }

        $finder = $this->createFinderFor('products');

        //$adapter = $finder->createPaginatorAdapter('*'.$query.'*');
        $productQuery = $this->createQueryForProducts($query, $categories, $priceRange);
        $adapter = $finder->createPaginatorAdapter($productQuery);

        return $this->paginator->paginate($adapter, $page, $limit);
    }

    private function createFinderFor($type)
    {
        return $this->container->get('fos_elastica.finder.app.'.$type);
    }

    private function createQueryForProducts($query, $categories = array(), $priceRange = array())
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
            $this->setCategoriesQuery($boolQuery, $categories);
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
        $priceRange = array_map(function($item){
            return floatval($item)*100.00;
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

    private function setCategoriesQuery(BoolQuery $boolQuery, array $categories)
    {
        $categoriesQuery = new Nested();
        $categoriesQuery->setPath('categories');
        
        $categoriesBool = new BoolQuery();
        $categoriesBool->addMust(new Terms('categories.id', $categories));

        $enableQuery = new Term();
        $enableQuery->setTerm('categories.enabled', true);
        $categoriesBool->addFilter($enableQuery);

        $categoriesQuery->setQuery($categoriesBool);

        $boolQuery->addMust($categoriesQuery);
    }
}
