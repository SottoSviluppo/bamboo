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

/**
 * Defines all the search methods on admin
 */
class SearchController extends Controller
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
        $request = $this->getRequest();
        $query = $request->query->get('q');

        if(empty($query)){
            return $this->redirect($this->generateUrl('admin_order_list'));
        }

        $page = $request->query->get('page');
        if (empty($page)) {
            $page = 1;
        }
        
        $limit = $request->query->get('limit');
        if (empty($limit)) {
            $limit = null;
        }

        $dr = $request->query->get('dr');
        $dateRange = [];
        if (!empty($dr)) {
            $dateRange = explode(',', $dr);
        }

        return [
            'query' => $query,
            'page' => $page,
            'limit' => $limit,
            'dateRange' => $dateRange
        ];
    }

    /**
    * @Template("AdminSearchBundle:Search:customers.html.twig")
    */
    public function searchCustomersAction()
    {
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
}
