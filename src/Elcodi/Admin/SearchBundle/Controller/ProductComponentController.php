<?php

namespace Elcodi\Admin\SearchBundle\Controller;

use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ProductComponentController extends AbstractAdminController {
	private $service;

	public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
		parent::setContainer($container);
		$this->service = $this->get('elcodi_admin.admin_search');
	}

	/**
	 * @Template("AdminSearchBundle:Search:productListComponent.html.twig")
	 */
	public function listComponentAction($page, $limit = null) {
		$query = $this->getRequest()->query->get('query');
		$products = $this->service->searchProducts($query, $page, $limit);
		return [
			'query' => $query,
			'paginator' => $products,
			'page' => $page,
			'limit' => $this->service->getLimit(),
			'orderByField' => 'id',
			'orderByDirection' => 'DESC',
			'totalPages' => ceil($products->getTotalItemCount() / $this->service->getLimit()),
			'totalElements' => $products->getTotalItemCount(),
		];
	}
}