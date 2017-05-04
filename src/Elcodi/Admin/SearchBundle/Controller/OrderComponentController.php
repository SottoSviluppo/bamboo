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

        $orderState = $request->get('orderState');
        $shippingState = $request->get('shippingState');
        $customerEmail = $request->get('customerEmail');
        $paymentMethod = $request->get('paymentMethod');
        $dateFrom = $request->get('dateFrom');
        $dateTo = $request->get('dateTo');
        $idFrom = $request->get('idFrom');
        $idTo = $request->get('idTo');
        $template = $request->get('template', 'AdminCartBundle:Order:listComponent.html.twig');
        // var_dump($request->request->all()); //POST
        // var_dump($request->query->all()); //GET
        // die();

        if ($query === "_") {
            $query = null;
        }

        $dateRange = $this->service->getRange($dateFrom, $dateTo);
        $idRange = $this->service->getRange($idFrom, $idTo);

        $this->service->setPage($page);
        $this->service->setLimit($limit);
        $this->service->addQuery($query);
        $this->service->addDateRange($dateRange);
        $this->service->addOrderPaymentState($orderState);
        $this->service->addOrderShippingState($shippingState);
        $this->service->addIdRange($idRange);
        $this->service->addCustomerEmail($customerEmail);
        $this->service->addOrderPaymentMethod($paymentMethod);
        $this->service->addCountry(0); //WTF

        // $this->service->printDebug();

        $orders = $this->service->getPaginator();

        $results = [
            'query' => $query,
            'dateRange' => $dateRange,
            'orderState' => $orderState,
            'shippingState' => $shippingState,
            'customerEmail' => $customerEmail,
            'paymentMethod' => $paymentMethod,
            'template' => $template,
            'idRange' => $idRange,
            'paginator' => $orders,
            'page' => $page,
            'limit' => $this->service->getLimit(),
            'orderByField' => 'id',
            'orderByDirection' => 'DESC',
            'totalPages' => ceil($orders->getTotalItemCount() / $this->service->getLimit()),
            'totalElements' => $orders->getTotalItemCount(),
        ];

        return $this->render(
            $template,
            $results
        );
    }

}
