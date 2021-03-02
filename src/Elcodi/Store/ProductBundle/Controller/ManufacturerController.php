<?php

/*
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014-2016 Elcodi.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 */

namespace Elcodi\Store\ProductBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Elcodi\Component\Product\Entity\Interfaces\ManufacturerInterface;
use Elcodi\Component\Product\Entity\Interfaces\PurchasableInterface;
use Elcodi\Component\Product\Repository\PurchasableRepository;
use Elcodi\Store\CoreBundle\Controller\Traits\TemplateRenderTrait;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as AnnotationEntity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manufacturer controller
 *
 * @Route(
 *      path = ""
 * )
 */
class ManufacturerController extends Controller {
	use TemplateRenderTrait;

	/**
	 * Renders the manufacturer nav component
	 *
	 * @return Response Response
	 *
	 * @Route(
	 *      path = "/manufacturers/nav",
	 *      name = "store_manufacturers_nav",
	 *      methods = {"GET"}
	 * )
	 */
	public function navAction() {
		$masterRequest = $this
			->get('request_stack')
			->getMasterRequest();

		$currentManufacturer = $this->getCurrentManufacturerGivenRequest($masterRequest);

		// $categoryTree = $this
		// 	->get('elcodi_store.store_category_tree')
		// 	->load();

		return $this->renderTemplate(
			'Subpages:manufacturer-nav.html.twig',
			[
				'currentManufacturer' => $currentManufacturer,
				// 'categoryTree' => $categoryTree,
			]
		);
	}

	/**
	 * Render all manufacturer purchasables
	 *
	 * @param ManufacturerInterface $manufacturer Manufacturer
	 *
	 * @return Response Response
	 *
	 * @Route(
	 *      path = "manufacturer/{slug}/{id}/{page}/{limit}/{orderByField}/{orderByDirection}",
	 *      name = "store_manufacturer_purchasables_list",
	 *      requirements = {
	 *          "slug" = "[a-zA-Z0-9-]+",
	 *          "id" = "\d+",
	 *          "page" = "\d*",
	 *          "limit" = "\d*",
	 *      },
	 *      defaults = {
	 *          "page" = "1",
	 *          "limit" = "10",
	 *          "orderByField" = "id",
	 *          "orderByDirection" = "DESC",
	 *      },
	 *      methods = {"GET"}
	 * )
	 *
	 * @AnnotationEntity(
	 *      class = "elcodi.entity.manufacturer.class",
	 *      name = "manufacturer",
	 *      mapping = {
	 *          "id" = "~id~",
	 *          "enabled" = true,
	 *      }
	 * )
	 */
	public function viewAction(ManufacturerInterface $manufacturer, $slug, $id, $page, $limit, $orderByField, $orderByDirection, Request $request) {
		//item_for_page parametro di configurazione, indica il numero di elementi per pagina, se settato imposta la vairabile $limit con un valore diverso dal default = 10
		if ($this->container->hasParameter('item_for_page')) {
			$limit = $this->getParameter('item_for_page');
		}

		/**
		 * We must check that the product slug is right. Otherwise we must
		 * return a Redirection 301 to the right url
		 */
		if ($slug !== $manufacturer->getSlug()) {
			return $this->redirectToRoute('store_manufacturer_purchasables_list', [
				'id' => $manufacturer->getId(),
				'slug' => $manufacturer->getSlug(),
			], 301);
		}

		// if ($manufacturer->getSubCategories()->count() > 0) {
		// 	return $this->redirectToRoute('store_subcategories_list', array('id' => $manufacturer->getId()));
		// }

		/**
		 * @var ManufacturerRepository $manufacturerRepository
		 * @var PurchasableRepository $purchasableRepository
		 */
		// $manufacturerRepository = $this->get('elcodi.repository.manufacturer');
		$purchasableRepository = $this->get('elcodi.repository.purchasable');

		// $manufacturers = array_merge(
		// 	[$manufacturer],
		// 	$categoryRepository->getChildrenCategories($manufacturer)
		// );

		$purchasablesQuery = $purchasableRepository->getAllFromManufactures($manufacturer->getId());

		$paginator = new Paginator($purchasablesQuery);

		$paginator->getQuery()
			->setFirstResult($limit * ($request->get('page') - 1)) // Offset
			->setMaxResults($limit); // Limit

		$maxPages = ceil($paginator->count() / $limit);

		return $this->renderTemplate(
			'Pages:manufacturer-view.html.twig',
			[
				'manufacturer' => $manufacturer,
				'purchasables' => $paginator,
				'currentPage' => $request->get('page'),
				'limit' => $limit,
				'totalPages' => $maxPages,
			]
		);
	}

	/**
	 * Given a request, return the current highlight-able manufacturer
	 *
	 * @param Request $request Request
	 *
	 * @return ManufacturerInterface|null
	 */
	protected function getCurrentManufacturerGivenRequest(Request $request) {
		$masterRoute = $request->get('_route');
		$manufacturer = null;

		/**
		 * @var ManufacturerInterface $manufacturer
		 * @var PurchasableInterface $purchasable
		 */
		if ($masterRoute === 'store_purchasable_view') {
			$purchasableId = $request->get('id');
			$productRepository = $this->get('elcodi.repository.purchasable');

			$purchasable = $productRepository->find($purchasableId);
			$manufacturer = $purchasable->getManufacturer();
		} elseif ($masterRoute === 'store_manufacturer_purchasables_list') {
			$manufacturerId = $request->get('id');
			$manufacturerRepository = $this->get('elcodi.repository.manufacturer');
			$manufacturer = $manufacturerRepository->find($manufacturerId);
		}

		return $manufacturer;
	}
}
