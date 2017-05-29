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

        $orders = $ordersPaginator->getItems();
        $orderCoupons = $this->getCouponsFromOrders($orders);

        $results = [
            'paginator' => $ordersPaginator,
            'orderByField' => 'id',
            'orderByDirection' => 'DESC',
            'totalPages' => ceil($ordersPaginator->getTotalItemCount() / $limit),
            'totalElements' => $ordersPaginator->getTotalItemCount(),
            'countries' => $countries,
            'orderCoupons' => $orderCoupons,
        ];
        $results = array_merge($results, $searchParameters);

        return $this->render(
            $searchParameters['template'],
            $results
        );
    }

    public function getCouponsFromOrders($orders)
    {
        $coupons = array();
        foreach ($orders as $order) {
            //Cerca i coupon associati all'ordine passato
            $orderCoupons = $this->get('elcodi.repository.order_coupon')->findOrderCouponsByOrder($order);
            if (empty($orderCoupons)) {
                $coupon = null;
            } else {
                $coupon = $orderCoupons[0]->getCoupon();
            }
            //Array dei coupons associati agli ordini, array chiave valore, dove la chiave è l'id dell'ordine mentre il valore è il coupon se presente, altrimenti ha valore NULL
            $coupons[$order->getId()] = $coupon;
        }
        return $coupons;
    }

}
