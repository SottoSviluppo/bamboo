<?php

namespace Elcodi\Admin\SearchBundle\Controller;

use Mmoreram\ControllerExtraBundle\Annotation\Entity as AnnotationEntity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Elcodi\Component\Product\Entity\Interfaces\CategoryInterface;
use Elcodi\Component\Product\Entity\Interfaces\PurchasableInterface;
use Elcodi\Component\Product\Repository\CategoryRepository;
use Elcodi\Component\Product\Repository\PurchasableRepository;
use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;

/**
 * Defines all the search methods on admin
 */
class SearchController extends AbstractAdminController
{
    private $service;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container=null)
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

        $request = $this->getRequest();
        $query = $request->query->get('q');

        if(empty($query)){
            return $this->redirect($this->generateUrl('admin_product_list'));
        }

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
            'limit' => $limit
        ];
    }

    /**
    * @Template("AdminSearchBundle:Search:orders.html.twig")
    */
    public function searchOrdersAction()
    {
        if (!$this->canRead('order')) {
            $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
            return $this->redirect($this->generateUrl('admin_homepage'));
        }

        $request = $this->getRequest();
        $query = $request->query->get('q');

        if (empty($query)) {
            $query = "_";
        }

        $page = $request->query->get('page');
        if (empty($page)) {
            $page = 1;
        }
        
        $limit = $request->query->get('limit');
        if (empty($limit)) {
            $limit = $this->getParameter('item_for_page');
        }

        $dateFrom = $request->query->get('datefrom');
        $dateTo = $request->query->get('dateto');

        return [
            'query' => $query,
            'page' => $page,
            'limit' => $limit,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ];
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

        $request = $this->getRequest();
        $query = $request->query->get('q');

        if(empty($query)){
            return $this->redirect($this->generateUrl('admin_customer_list'));
        }

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
            'limit' => $limit
            /*'count' => count($products),
            'purchasables' => $products,*/
        ];
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

        $request = $this->getRequest();
        $query = $request->query->get('q');

        if(empty($query)){
            return $this->redirect($this->generateUrl('admin_manufacturer_list'));
        }

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
            'limit' => $limit
        ];
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

        $request = $this->getRequest();
        $query = $request->query->get('q');

        if(empty($query)){
            return $this->redirect($this->generateUrl('admin_coupon_list'));
        }

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
            'limit' => $limit
        ];
    }
}
