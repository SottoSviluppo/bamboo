<?php

namespace Elcodi\Admin\SearchBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Product\Entity\Interfaces\ProductInterface;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Form as FormAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Paginator as PaginatorAnnotation;
use Mmoreram\ControllerExtraBundle\ValueObject\PaginatorAttributes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormView;

class CouponComponentController extends AbstractAdminController
{
    private $service;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->service = $this->get('elcodi_admin.admin_search');
    }

    /**
     * @Template("AdminCouponBundle:Coupon:listComponent.html.twig")
     */
    public function listComponentAction($query, $page, $limit = null)
    {
        $coupons = $this->service->searchCoupons($query, $page, $limit);
        return [
            'query' => $query,
            'paginator' => $coupons,
            'page' => $page,
            'limit' => $this->service->getLimit(),
            'orderByField' => 'id',
            'orderByDirection' => 'DESC',
            'totalPages' => ceil($coupons->getTotalItemCount() / $this->service->getLimit()),
            'totalElements' => $coupons->getTotalItemCount(),
        ];
    }
}
