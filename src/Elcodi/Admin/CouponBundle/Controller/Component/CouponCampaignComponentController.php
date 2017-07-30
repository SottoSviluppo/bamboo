<?php

namespace Elcodi\Admin\CouponBundle\Controller\Component;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Coupon\Entity\Interfaces\CouponCampaignInterface;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Form as FormAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Paginator as PaginatorAnnotation;
use Mmoreram\ControllerExtraBundle\ValueObject\PaginatorAttributes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminCouponCampaignComponentController.
 *
 * @Route(
 *      path = "/%bamboo_admin_prefix%/coupon_campaign",
 * )
 */
class CouponCampaignComponentController extends AbstractAdminController
{
    /**
     * Component for entity list.
     *
     * As a component, this action should not return all the html macro, but
     * only the specific component
     *
     * @param Paginator           $paginator           Paginator instance
     * @param PaginatorAttributes $paginatorAttributes Paginator attributes
     * @param int                 $page                Page
     * @param int                 $limit               Limit of items per page
     * @param string              $orderByField        Field to order by
     * @param string              $orderByDirection    Direction to order by
     *
     * @return array Result
     *
     * @Route(
     *      path = "s/component/{page}/{limit}/{orderByField}/{orderByDirection}",
     *      name = "admin_coupon_campaign_list_component",
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
     * )
     * @Template("AdminCouponBundle:CouponCampaign:listComponent.html.twig")
     * @Method({"GET"})
     *
     * @PaginatorAnnotation(
     *      attributes = "paginatorAttributes",
     *      class = "elcodi.entity.coupon_campaign.class",
     *      page = "~page~",
     *      limit = "~limit~",
     *      orderBy = {
     *          {"x", "~orderByField~", "~orderByDirection~"}
     *      }
     * )
     */
    public function listComponentAction(
        Paginator $paginator,
        PaginatorAttributes $paginatorAttributes,
        $page,
        $limit,
        $orderByField,
        $orderByDirection
    ) {
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

    /**
     * New element component action.
     *
     * As a component, this action should not return all the html macro, but
     * only the specific component
     *
     * @param FormView                $formView       Form view
     * @param CouponCampaignInterface $couponCampaign CouponCampaign
     *
     * @return array Result
     *
     * @Route(
     *      path = "/{id}/component",
     *      name = "admin_coupon_campaign_edit_component",
     *      requirements = {
     *          "id" = "\d+",
     *      }
     * )
     * @Route(
     *      path = "/new/component",
     *      name = "admin_coupon_campaign_new_component",
     *      methods = {"GET"}
     * )
     * @Template("AdminCouponBundle:CouponCampaign:editComponent.html.twig")
     * @Method({"GET"})
     *
     * @EntityAnnotation(
     *      class = {
     *          "factory" = "elcodi.factory.coupon_campaign",
     *          "method" = "create",
     *          "static" = false
     *      },
     *      name = "couponCampaign",
     *      mapping = {
     *          "id" = "~id~"
     *      },
     *      mappingFallback = true,
     *      persist = true
     * )
     * @FormAnnotation(
     *      class = "elcodi_admin_coupon_campaign_form_type_coupon_campaign",
     *      name  = "form",
     *      entity = "couponCampaign",
     *      handleRequest = true,
     *      validate = "isValid"
     * )
     */
    public function editComponentAction(
        Form $form,
        CouponCampaignInterface $couponCampaign,
        Request $request
    ) {
        return [
            'couponCampaign' => $couponCampaign,
            'form' => $form->createView(),
        ];
    }
}
