<?php

namespace Elcodi\Admin\SearchBundle\Controller;

use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Product\Entity\Interfaces\CategoryInterface;
use Elcodi\Component\Product\Entity\Interfaces\PurchasableInterface;
use Elcodi\Component\Product\Repository\CategoryRepository;
use Elcodi\Component\Product\Repository\PurchasableRepository;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as AnnotationEntity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines all the search methods on admin
 */
class SearchController extends AbstractAdminController
{
    private $service;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->service = $this->get('elcodi_admin.admin_search');
    }

    /**
     * @Template("AdminSearchBundle:Search:products.html.twig")
     */
    public function searchProductsAction()
    {
        if (!$this->canRead('product')) {
            $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
            return $this->redirect($this->generateUrl('admin_homepage'));
        }

        $parameters = $this->getParameters();

        if (empty($parameters['query'])) {
            return $this->redirect($this->generateUrl('admin_product_list'));
        }

        return $parameters;
    }

    /**
     * @Template("AdminCartBundle:Order:list.html.twig")
     */
    public function searchOrdersAction()
    {
        if (!$this->canRead('order')) {
            $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
            return $this->redirect($this->generateUrl('admin_homepage'));
        }

        $parameters = $this->getParameters();
        if (empty($parameters['query'])) {
            $parameters['query'] = "_";
        }

        $request = $this->getRequest();

        $parameters['dateFrom'] = $request->query->get('datefrom');
        $parameters['dateTo'] = $request->query->get('dateto');
        $parameters['orderState'] = $request->query->get('orderState');
        $parameters['countryId'] = $request->query->get('countryId');
        $parameters['shippingState'] = $request->query->get('shippingState');
        $parameters['customerEmail'] = $request->query->get('customerEmail');
        $parameters['paymentMethod'] = $request->query->get('paymentMethod');
        $parameters['template'] = $request->query->get('template');
        $parameters['idFrom'] = $request->query->get('idFrom');
        $parameters['idTo'] = $request->query->get('idTo');
        $parameters['submit'] = $request->query->get('submit');

        // excel
        if ($submit == 'Excel') {
            $searchParameters = $this->get('elcodi_admin.order.admin_search')->getSearchParameters($request);
            $searchParameters['query'] = $query;
            $searchParameters['limit'] = 10000;
            $service = $this->get('elcodi_admin.order.admin_search')->prepareSearchService($searchParameters);

            return $this->get('elcodi.excel_manager.order')->getExcelFromOrders($service->getResult());
        }

        return $parameters;
    }

    /**
     * @Template("AdminSearchBundle:Search:customers.html.twig")
     */
    public function searchCustomersAction()
    {
        if (!$this->canRead('customer')) {
            $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
            return $this->redirect($this->generateUrl('admin_homepage'));
        }

        $parameters = $this->getParameters();

        if (empty($parameters['query'])) {
            return $this->redirect($this->generateUrl('admin_customer_list'));
        }

        return $parameters;
    }

    /**
     * @Template("AdminSearchBundle:Search:manufacturers.html.twig")
     */
    public function searchManufacturersAction()
    {
        if (!$this->canRead('manufacturer')) {
            $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
            return $this->redirect($this->generateUrl('admin_homepage'));
        }

        $parameters = $this->getParameters();

        if (empty($parameters['query'])) {
            return $this->redirect($this->generateUrl('admin_manufacturer_list'));
        }

        return $parameters;
    }

    /**
     * @Template("AdminSearchBundle:Search:coupons.html.twig")
     */
    public function searchCouponsAction()
    {
        if (!$this->canRead('coupon')) {
            $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
            return $this->redirect($this->generateUrl('admin_homepage'));
        }

        $parameters = $this->getParameters();

        if (empty($parameters['query'])) {
            return $this->redirect($this->generateUrl('admin_coupon_list'));
        }

        return $parameters;
    }

    private function getParameters()
    {
        $request = $this->getRequest();

        $query = $request->query->get('q');
        $page = $request->query->get('page');
        if (empty($page)) {
            $page = 1;
        }

        $limit = $request->query->get('limit');
        if (empty($limit)) {
            $limit = null;
        }

        return [
            'query' => $query,
            'page' => $page,
            'limit' => $limit,
        ];
    }
}
