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
            throw $this->createNotFoundException('Please, specify a query');
        }
        
        return [
            'query' => $query,
            /*'count' => count($products),
            'purchasables' => $products,*/
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
            throw $this->createNotFoundException('Please, specify a query');
        }

        return [
            'query' => $query,
            /*'count' => count($products),
            'purchasables' => $products,*/
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
            throw $this->createNotFoundException('Please, specify a query');
        }

        return [
            'query' => $query,
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
            throw $this->createNotFoundException('Please, specify a query');
        }

        return [
            'query' => $query,
            /*'count' => count($products),
            'purchasables' => $products,*/
        ];
    }
}