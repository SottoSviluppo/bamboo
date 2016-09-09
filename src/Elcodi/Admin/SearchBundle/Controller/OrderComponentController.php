<?php

namespace Elcodi\Admin\SearchBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Form as FormAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Paginator as PaginatorAnnotation;
use Mmoreram\ControllerExtraBundle\ValueObject\PaginatorAttributes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormView;

use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Product\Entity\Interfaces\ProductInterface;

class OrderComponentController extends AbstractAdminController
{
    private $service;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container=null)
    {
        parent::setContainer($container);
        $this->service = $this->get('elcodi_admin.admin_search');
    }

    /**
    * @Template("AdminSearchBundle:Search:orderListComponent.html.twig")
    */
    public function listComponentAction($query, $page, $limit = null)
    {
        $orders = $this->service->searchOrders($query, $page, $limit);
        return [
                'query' => $query,
                'paginator' => $orders,
                'page' => $page,
                'limit' => $this->service->getLimit(),
                'orderByField' => '',
                'orderByDirection' => '',
                'totalPages' => ceil($orders->getTotalItemCount()/$this->service->getLimit()),
                'totalElements'=> $orders->getTotalItemCount(),
            ];
    }
}