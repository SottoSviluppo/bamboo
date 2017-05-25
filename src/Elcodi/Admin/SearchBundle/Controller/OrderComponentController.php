<?php

namespace Elcodi\Admin\SearchBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Product\Entity\Interfaces\ProductInterface;
use Elcodi\Store\CoreBundle\Controller\Traits\TemplateRenderTrait;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Form as FormAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Paginator as PaginatorAnnotation;
use Mmoreram\ControllerExtraBundle\ValueObject\PaginatorAttributes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormView;

class OrderComponentController extends AbstractAdminController
{
    // use TemplateRenderTrait;

    private $service;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->service = $this->get('elcodi_admin.order.admin_search');
    }

    public function listComponentAction($query, $page, $limit)
    {
        $request = $this->get('request');

        $searchParameters = $this->get('elcodi_admin.order.admin_search')->getSearchParameters($request);
        $searchParameters['query'] = $query;
        $searchParameters['page'] = $page;
        $searchParameters['limit'] = $limit;

        $ordersPaginator = $this->get('elcodi_admin.order.admin_search')->getOrdersPaginator($searchParameters);
        $ordersPaginator->setPageRange(11);
        $ordersPaginator->setItemNumberPerPage($limit);

        $countries = $this->get('elcodi.repository.country')->findByEnabled(true);

        $results = [
            'paginator' => $ordersPaginator,
            'orderByField' => 'id',
            'orderByDirection' => 'DESC',
            'totalPages' => ceil($ordersPaginator->getTotalItemCount() / $limit),
            'totalElements' => $ordersPaginator->getTotalItemCount(),
            'countries' => $countries,
        ];
        $results = array_merge($results, $searchParameters);

        return $this->render(
            $searchParameters['template'],
            $results
        );
    }

}
