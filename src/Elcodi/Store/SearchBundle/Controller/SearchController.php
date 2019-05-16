<?php

namespace Elcodi\Store\SearchBundle\Controller;

use Elcodi\Store\CoreBundle\Controller\Traits\TemplateRenderTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Defines all the search methods on frontend
 */
class SearchController extends Controller {
	use TemplateRenderTrait;

	private $service;

	public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
		parent::setContainer($container);
		$this->service = $this->get('elcodi_store.store_search');
	}

	public function searchProductsAction() {
		$request = $this->getRequest();
		$query = $request->query->get('q');

		$categories = [];
		$cat = $request->query->get('cat');
		if (!empty($cat)) {
			$categories = explode(',', $cat);
		}

		$priceRange = [];
		$pr = $request->query->get('pr');
		if (!empty($pr)) {
			$priceRange = explode(',', $pr);
		}

		$page = $request->query->get('page');
		if (empty($page)) {
			$page = 1;
		}

		$limit = $request->query->get('limit');
		if (empty($limit)) {
			$limit = null;
		}

		$sortField = $request->query->get('sortField', '');
		$sortDirection = $request->query->get('sortDirection', 'ASC');

		if ($sortField != '') {
			$this->service->setSortArray(array(
				$sortField => $sortDirection,
			)
			);
		}

		$categoryConnector = $request->query->get('catcn');
		if (empty($categoryConnector)) {
			$categoryConnector = null;
		}

		$products = $this->service->searchProducts($query, $page, $limit, $categories, $priceRange, $categoryConnector, $sortField);

		return $this->renderTemplate(
			'Pages:search-products.html.twig',
			[
				'query' => $query,
				'count' => $products->getTotalItemCount(),
				'purchasables' => $products,
			]
		);
	}
}
