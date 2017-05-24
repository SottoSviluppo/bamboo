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

        $searchParameters = $this->getSearchParameters($request);
        $searchParameters['query'] = $query;
        $searchParameters['page'] = $page;
        $searchParameters['limit'] = $limit;

        $ordersPaginator = $this->getOrdersPaginator($searchParameters);
        $ordersPaginator->setPageRange(11);

        $countries = $this->get('elcodi.repository.country')->findByEnabled(true);

        $results = [
            'paginator' => $ordersPaginator,
            'orderByField' => 'id',
            'orderByDirection' => 'DESC',
            'totalPages' => ceil($ordersPaginator->getTotalItemCount() / $limit),
            'totalElements' => $ordersPaginator->getTotalItemCount(),
            'countries' => $countries,
        ];
        $searchParameters = $this->getSearchParameters($request);
        $results = array_merge($results, $searchParameters);

        return $this->render(
            $searchParameters['template'],
            $results
        );
    }

    public function getSearchParameters($request)
    {
        $orderState = $request->get('orderState');
        $shippingState = $request->get('shippingState');
        $customerEmail = $request->get('customerEmail');
        $paymentMethod = $request->get('paymentMethod');
        $dateFrom = $request->get('dateFrom');
        $dateTo = $request->get('dateTo');
        $idFrom = $request->get('idFrom');
        $idTo = $request->get('idTo');
        $countryId = $request->get('countryId', 0);
        $template = $request->get('template', 'AdminCartBundle:Order:listComponent.html.twig');

        $service = $this->get('elcodi_admin.order.admin_search');

        $dateRange = $service->getRange($dateFrom, $dateTo);
        $idRange = $service->getRange($idFrom, $idTo);

        return [
            'dateRange' => $dateRange,
            'orderState' => $orderState,
            'shippingState' => $shippingState,
            'countryId' => $countryId,
            'customerEmail' => $customerEmail,
            'paymentMethod' => $paymentMethod,
            'template' => $template,
            'idRange' => $idRange,
        ];
    }

    public function getOrdersPaginator($searchParameters)
    {
        $service = $this->prepareSearchService($searchParameters);
        $ordersPaginator = $service->getPaginator();

        return $ordersPaginator;
    }

    public function prepareSearchService($searchParameters)
    {
        if ($searchParameters['query'] === "_") {
            $searchParameters['query'] = null;
        }

        $service = $this->get('elcodi_admin.order.admin_search');

        if (array_key_exists('page', $searchParameters)) {
            $service->setPage($searchParameters['page']);
        }
        if (array_key_exists('limit', $searchParameters)) {
            $service->setLimit($searchParameters['limit']);
        }
        $service->addQuery($searchParameters['query']);
        $service->addDateRange($searchParameters['dateRange']);
        $service->addOrderPaymentState($searchParameters['orderState']);
        $service->addOrderShippingState($searchParameters['shippingState']);
        $service->addIdRange($searchParameters['idRange']);
        $service->addCustomerEmail($searchParameters['customerEmail']);
        $service->addOrderPaymentMethod($searchParameters['paymentMethod']);
        $service->addCountry($searchParameters['countryId']);
        return $service;
    }

}
