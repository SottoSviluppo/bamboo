<?php

/*
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014-2016 Elcodi.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author Aldo Chiecchia <zimage@tiscali.it>
 * @author Elcodi Team <tech@elcodi.com>
 */

namespace Elcodi\Store\CartBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Elcodi\Component\Cart\Entity\Interfaces\OrderInterface;
use Elcodi\Store\CoreBundle\Controller\Traits\TemplateRenderTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Order controllers
 *
 * @Security("has_role('ROLE_CUSTOMER')")
 * @Route(
 *      path = "/order",
 * )
 */
class OrderController extends Controller
{
    use TemplateRenderTrait;

    /**
     * Order view
     *
     * @param integer $id     Order id
     * @param boolean $thanks Thanks
     *
     * @return Response Response
     *
     * @Route(
     *      path = "/{id}",
     *      name = "store_order_view",
     *      requirements = {
     *          "orderId": "\d+"
     *      },
     *      defaults = {
     *          "thanks": false
     *      },
     *      methods = {"GET"}
     * )
     * @Route(
     *      path = "/{id}/thanks",
     *      name = "store_order_thanks",
     *      requirements = {
     *          "orderId": "\d+"
     *      },
     *      defaults = {
     *          "thanks": true
     *      },
     *      methods = {"GET"}
     * )
     */
    public function viewAction($id, $thanks)
    {
        $order = $this
            ->get('elcodi.repository.order')
            ->findOneBy([
                'id' => $id,
                'customer' => $this->getUser(),
            ]);

        if (!($order instanceof OrderInterface)) {
            throw $this->createNotFoundException('Order not found');
        }

        $orderCoupons = $this
            ->get('elcodi.repository.order_coupon')
            ->findOrderCouponsByOrder($order);

        return $this->renderTemplate(
            'Pages:order-view.html.twig',
            [
                'order' => $order,
                'orderCoupons' => $orderCoupons,
                'thanks' => $thanks,
            ]
        );
    }

    /**
     * Generic thank you page
     *
     * @param integer $id     Order id
     * @param boolean $thanks Thanks
     *
     * @return Response Response
     *
     * @Route(
     *      path = "/completed/thanks",
     *      name = "store_completed_order_generic",
     *      methods = {"GET"}
     * )
     */
    public function completedOrderGenericAction()
    {
        $orders = $this
            ->get('elcodi.repository.order')
            ->findBy([
                'customer' => $this->getUser(),
            ],
                [
                    'id' => 'DESC',
                ]
            );
        $lastOrder = $orders[0];

        // if order is not paid then redirect to correct order thank you page
        $lastPaymentState = $lastOrder->getPaymentLastStateLine()->getName();
        if ($lastPaymentState == 'unpaid') {
            return $this->redirect(
                $this->generateUrl('store_order_thanks', ['id' => $lastOrder->getId()])
            );
        }
        die();
    }

    /**
     * Order list with pagination.
     *
     * @return Response Response
     *
     * @Route(
     *      path = "s/{page}/{limit}/{orderByField}/{orderByDirection}",
     *      name = "store_order_list",
     *      requirements = {
     *          "page" = "\d*",
     *          "limit" = "\d*",
     *             "progress" = "all"
     *      },
     *      defaults = {
     *          "page" = "1",
     *          "limit" = "10",
     *          "orderByField" = "id",
     *          "orderByDirection" = "DESC",
     *      },
     *      methods = {"GET"}
     * )
     *
     */
    public function listAction($page, $limit, $orderByField, $orderByDirection, Request $request)
    {
        //item_for_page parametro di configurazione, indica il numero di elementi per pagina, se settato imposta la vairabile $limit con un valore diverso dal default = 10
        if ($this->container->hasParameter('item_for_page')) {
            $limit = $this->getParameter('item_for_page');
        }
        //Id dell'utente loggato
        $user = $this->get('security.context')->getToken()->getUser();
        $customer_id = $user->getId();

        //Ordini dell'utente loggato
        $orderRepository = $this->get('elcodi.repository.order');
        $queryBuilder = $orderRepository->createQueryBuilder('o');
        $ordersQuery = $queryBuilder->select('o')
            ->where('o.customer = :customer_id')
            ->setParameter('customer_id', $customer_id)
            ->orderBy('o.createdAt', 'DESC');

        $paginator = new Paginator($ordersQuery);

        $paginator->getQuery()
            ->setFirstResult($limit * ($request->get('page') - 1)) // Offset
            ->setMaxResults($limit); // Limit

        $maxPages = ceil($paginator->count() / $limit);

        return $this->renderTemplate(
            'Pages:order-list.html.twig',
            [
                'orders' => $paginator,
                'currentPage' => $request->get('page'),
                'limit' => $limit,
                'totalPages' => $maxPages,
            ]
        );

    }

    /**
     *
     * @Route(
     *      path = "/restore/fromorder/{id}",
     *      name = "restore_cart_from_order",
     *      methods = {"GET"}
     * )
     *
     */
    public function restoreCartFromOrderAction($id)
    {
        $this->get('elcodi.cart_restorer')->restoreCartFromOrderId($id);

        return $this->redirect(
            $this->generateUrl('store_checkout_address')
        );
    }
}
