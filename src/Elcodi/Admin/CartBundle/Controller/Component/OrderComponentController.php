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

namespace Elcodi\Admin\CartBundle\Controller\Component;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Cart\Entity\Interfaces\OrderInterface;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\ValueObject\PaginatorAttributes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class OrderComponentsController
 *
 * @Route(
 *      path = "order",
 * )
 */
class OrderComponentController extends AbstractAdminController
{
    /**
     * Component for entity list.
     *
     * As a component, this action should not return all the html macro, but
     * only the specific component
     *
     * @param Paginator           $paginator           Paginator instance
     * @param PaginatorAttributes $paginatorAttributes Paginator attributes
     * @param integer             $page                Page
     * @param integer             $limit               Limit of items per page
     * @param string              $orderByField        Field to order by
     * @param string              $orderByDirection    Direction to order by
     *
     * @return array Result
     *
     * @Route(
     *      path = "s/component/{page}/{limit}/{orderByField}/{orderByDirection}",
     *      name = "admin_order_list_component",
     *      requirements = {
     *          "page" = "\d*",
     *          "limit" = "\d*",
     *      },
     *      defaults = {
     *          "page" = "1",
     *          "limit" = "50",
     *          "orderByField" = "id",
     *          "orderByDirection" = "DESC",
     *      },
     *      methods = {"GET"}
     * )
     * @Template("AdminCartBundle:Order:listComponent.html.twig")
     *
     */
    public function listComponentAction(
        $page,
        $limit,
        $orderByField,
        $orderByDirection
    ) {
        $ordersRepository = $this->get('elcodi.repository.order');
        $queryBuilder = $ordersRepository->createQueryBuilder('o');
        $queryBuilder->innerJoin('o.paymentLastStateLine', 'p');
        $queryBuilder->orderBy('p.id', 'DESC');

        // if ($state != 'all') {
        //     $queryBuilder->where("p.name = '$state'");
        // }

        $paginator = new Paginator($queryBuilder, true);
        $offset = $limit * ($page - 1);
        $paginator
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $paginatorAttributes = $this->evaluateAttributes($paginator, $limit, $page);

        return [
            'paginator' => $paginator,
            'page' => $page,
            'limit' => $limit,
            'orderByField' => $orderByField,
            'orderByDirection' => $orderByDirection,
            'totalPages' => $paginatorAttributes->getTotalPages(),
            'totalElements' => $paginatorAttributes->getTotalElements(),
        ];
    }

    protected function evaluateAttributes(
        // Request $request,
        // AnnotationPaginator $annotation,
        Paginator $paginator,
        $limitPerPage,
        $page
    ) {
        $paginatorAttributes = new PaginatorAttributes();
        $total = $paginator->count();

        $paginatorAttributes
            ->setCurrentPage($page)
            ->setLimitPerPage($limitPerPage)
            ->setTotalElements($total)
            ->setTotalPages(ceil($total / $limitPerPage));

        // $request->attributes->set(
        //     trim($annotation->getAttributes()),
        //     $paginatorAttributes
        // );
        return $paginatorAttributes;
    }

    /**
     * New element component action
     *
     * As a component, this action should not return all the html macro, but
     * only the specific component
     *
     * @param OrderInterface $order Order
     *
     * @return array Result
     *
     * @Route(
     *      path = "/{id}/component",
     *      name = "admin_order_edit_component",
     *      requirements = {
     *          "id" = "\d+",
     *      },
     *      methods={"GET"}
     * )
     * @Template("AdminCartBundle:Order:editComponent.html.twig")
     *
     * @EntityAnnotation(
     *      class = "elcodi.entity.order.class",
     *      name = "order",
     *      mapping = {
     *          "id" = "~id~"
     *      }
     * )
     */
    public function editComponentAction(OrderInterface $order)
    {
        return [
            'order' => $order,
        ];
    }
}
