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
class SearchController extends Controller
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

        $products = $this->service->searchProducts($query, $page, $limit, $categories, $priceRange);

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
