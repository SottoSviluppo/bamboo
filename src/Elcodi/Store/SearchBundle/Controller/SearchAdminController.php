<?php

namespace Elcodi\Store\SearchBundle\Controller;

use Mmoreram\ControllerExtraBundle\Annotation\Entity as AnnotationEntity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Elcodi\Component\Product\Entity\Interfaces\CategoryInterface;
use Elcodi\Component\Product\Entity\Interfaces\PurchasableInterface;
use Elcodi\Component\Product\Repository\CategoryRepository;
use Elcodi\Component\Product\Repository\PurchasableRepository;
use Elcodi\Store\CoreBundle\Controller\Traits\TemplateRenderTrait;

/**
 * Defines all the search methods on frontend
 */
class SearchAdminController extends Controller
{
    use TemplateRenderTrait;

    private $service;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container=null)
    {
        parent::setContainer($container);
        $this->service = $this->get('elcodi_store.store_search');
    }

    public function searchProductsAction()
    {
        $request = $this->getRequest();
        $query = $request->query->get('q');

        if(empty($query)){
            throw $this->createNotFoundException('Please, specify a query');
        }

        $productsTmp = $this->service->searchProducts($query);
        $products = array_map(function($p){
            return $p->getName();
        }, $productsTmp);

        $html = "<pre>".print_r($products, true)."</pre>";
        return new Response($html);
    }

    public function searchOrdersAction()
    {
        $request = $this->getRequest();
        $query = $request->query->get('q');

        if(empty($query)){
            throw $this->createNotFoundException('Please, specify a query');
        }

        return new Response($query);
    }

    public function searchCustomersAction()
    {
        $request = $this->getRequest();
        $query = $request->query->get('q');

        if(empty($query)){
            throw $this->createNotFoundException('Please, specify a query');
        }

        $customersTmp = $this->service->searchCustomers($query);
        $customers = array_map(function($c){
            return [
                'firstName' => $c->getFirstname(),
                'lastName' => $c->getLastname(),
                'email' => $c->getEmail()
            ];
        }, $customersTmp);

        $html = "<pre>".print_r($customers, true)."</pre>";

        return new Response($html);
    }
}
