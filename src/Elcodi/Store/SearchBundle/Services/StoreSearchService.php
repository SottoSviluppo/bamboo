<?php

namespace Elcodi\Store\SearchBundle\Services;

use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\MultiMatch;
use Elastica\Query\Terms;
use Elastica\Query\NumericRange;
use Elastica\Query\Nested;

use Elcodi\Store\SearchBundle\Services\IStoreSearchService;

/**
 * Defines all the search methods
 */
class StoreSearchService implements IStoreSearchService
{
    private $container;
    private $prefix;

    function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container, $prefix)
    {
        $this->container = $container;
        $this->prefix = $prefix;
    }

    public function searchProducts($query, $categories = array(), $priceRange = array())
    {
        $productQuery = $this->createQueryForProducts($query, $categories, $priceRange);
        $finder = $this->createFinderFor('products');

        return $finder->find($productQuery);
    }

    private function createFinderFor($type)
    {
        return $this->container->get('fos_elastica.finder.app.'.$type);
    }

    private function createQueryForProducts($query, $categories = array(), $priceRange = array())
    {
        $boolQuery = new BoolQuery();

        $fieldQuery = new MultiMatch();
        $fieldQuery->setQuery($query);
        $fieldQuery->setFields([
           'name', 'sku', 'shortDescription', 'description' 
        ]);
        $fieldQuery->setOperator();
        $fieldQuery->setType('best_fields');

        $boolQuery->addMust($fieldQuery);

        $this->setNestedQueriesForProduct($boolQuery, $query);

        //$categories = [1];
        if (!empty($categories)) {
            $this->setCategoriesQuery($boolQuery, $categories);
        }

        //$priceRange = [1000, 2000];
        if (!empty($priceRange)) {
            $this->setPriceRangeQuery($boolQuery, $priceRange);
        }

        return $boolQuery;
    }

    private function setNestedQueriesForProduct(BoolQuery $boolQuery, $query)
    {
        $categories = new Nested();
        $categories->setPath('categories');
        $categories->setQuery(new Match('name', $query));

        $boolQuery->addShould($categories);

        $variants = new Nested();
        $variants->setPath('variants');
        $variantsQuery = new MultiMatch();
        $variantsQuery->setQuery($query);
        $variantsQuery->setFields([
           'name', 'sku', 'shortDescription', 'description' 
        ]);
        $variantsQuery->setOperator();
        $variantsQuery->setType('best_fields');
        $variants->setQuery($variantsQuery);

        $boolQuery->addShould($variants);
    }

    private function setPriceRangeQuery(BoolQuery $boolQuery, array $priceRange)
    {
        $price = new Nested();
        $price->setPath('price');
        $price->setQuery(new NumericRange('amount', $priceRange));
        
        $boolQuery->addMust($price);
    }

    private function setCategoriesQuery(BoolQuery $boolQuery, array $categories)
    {
        $categoriesQuery = new Nested();
        $categoriesQuery->setPath('categories');
        $categoriesQuery->setQuery(new Terms('name', $categories));

        $boolQuery->addMust($categoriesQuery);
    }
}
