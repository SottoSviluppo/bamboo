<?php

namespace Elcodi\Store\SearchBundle\Services;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MultiMatch;
use Elastica\Query\Nested;
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
class StoreSearchService implements IStoreSearchService {
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

	public function setSortArray($sortArray) {
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

	public function searchProducts($query, $page = 1, $limit = null, array $categories = array(), array $priceRange = array(), $categoryConnector = null) {
		/*if ($query == '') {
			$query = '';
		}*/

		if (empty($limit)) {
			$limit = $this->itemsPerPage;
		}

		if (empty($categoryConnector)) {
			$categoryConnector = $this->categoryDefaultConnector;
		}

		$finder = $this->createFinderFor('products');

		$productQuery = $this->createQueryForProducts($query, $categories, $priceRange, $categoryConnector);

		//Per vedere la query che esegue ELASTICSEARCH decommenta sotto.
		//$json=json_encode($productQuery->toArray());
		//echo $json;

		$adapter = $finder->createPaginatorAdapter($productQuery);
		return $this->paginator->paginate($adapter, $page, $limit);
	}

	protected function createFinderFor($type) {
		return $this->container->get('fos_elastica.finder.app.' . $type);
	}

	protected function createQueryForProducts($query, array $categories = array(), array $priceRange = array(), $categoryConnector) {
		$query = strtolower($query);
		$boolQuery = $this->getBoolQueryForProducts($query, $categories, $priceRange, $categoryConnector);

		// ordina i risultati della ricerca per principalCategory e per id del prodotto
		$finalQuery = new Query($boolQuery);

		if (empty($query) and !empty($categories)) {
			$finalQuery->setSort($this->sortArray);
		}

		return $finalQuery;
	}

	protected function getBoolQueryForProducts($query, array $categories = array(), array $priceRange = array(), $categoryConnector) {
		$boolQuery = new BoolQuery();

		$enableQuery = new Term();
		$enableQuery->setTerm('enabled', true);
		$boolQuery->addFilter($enableQuery);

		$privateQuery = new Term();
		$privateQuery->setTerm('private', false);
		$boolQuery->addFilter($privateQuery);

		if (!empty($query)) {
			$query = trim($query);
			$baseQuery = new BoolQuery();

			if ($this->productPartialSearch) {
				$productsPartialQuery = $this->setQueryForPartialProducts($query);
				$baseQuery->addShould($productsPartialQuery);
			}
			//else {
			$productsQuery = $this->setQueryForProducts($query);
			//}
			$baseQuery->addShould($productsQuery);

			if ($this->searchProductsWithVariants) {
				$variantsQuery = $this->setNestedQueriesForVariants($query);
				$baseQuery->addShould($variantsQuery);
			}
			$boolQuery->addMust($baseQuery);
		}

		if (!empty($categories)) {
			$this->setCategoriesQuery($boolQuery, $categories, $categoryConnector);
		}

		if (!empty($priceRange)) {
			$this->setPriceRangeQuery($boolQuery, $priceRange);
		}

		return $boolQuery;
	}

	protected function setQueryForProducts($query) {
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

	/**
	 * Query per la ricerca parziale dei prodotti, cercado in name shortDescription, description, sku
	 * @param string $query stringa da cercare
	 */
	protected function setQueryForPartialProducts($query) {
		$wildcardBool = new BoolQuery();

		$wildcardBool->addShould(new Wildcard('name', '*' . $query . '*'));
		$wildcardBool->addShould(new Wildcard('shortDescription', '*' . $query . '*'));
		$wildcardBool->addShould(new Wildcard('description', '*' . $query . '*'));
		$wildcardBool->addShould(new Wildcard('sku', '*' . $query . '*'));

		return $wildcardBool;
	}

	protected function setNestedQueriesForVariants($query) {
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

	protected function setPriceRangeQuery(BoolQuery $boolQuery, array $priceRange) {
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

	protected function setCategoriesQuery(BoolQuery $boolQuery, array $categories, $categoryConnector) {
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
