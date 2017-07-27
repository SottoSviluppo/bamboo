<?php

namespace Elcodi\Admin\CouponBundle\Controller;

use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Core\Entity\Interfaces\EnabledInterface;
use Elcodi\Component\Coupon\Entity\Interfaces\CouponCampaignInterface;
use Elcodi\Component\Media\Entity\Interfaces\ImageInterface;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Form as FormAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Controller for CouponCampaign.
 *
 * @Route(
 *      path = "/%bamboo_admin_prefix%/coupon_campaign",
 * )
 */
class CouponCampaignController extends AbstractAdminController
{
    /**
     * List elements of certain entity type.
     *
     * This action is just a wrapper, so should never get any data,
     * as this is component responsibility
     *
     * @param Request $request          Request
     * @param int     $page             Page
     * @param int     $limit            Limit of items per page
     * @param string  $orderByField     Field to order by
     * @param string  $orderByDirection Direction to order by
     *
     * @return array Result
     *
     * @Route(
     *      path = "s/{page}/{limit}/{orderByField}/{orderByDirection}",
     *      name = "admin_coupon_campaign_list",
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
     * @Template
     * @Method({"GET"})
     */
    public function listAction(
        Request $request,
        $page,
        $limit,
        $orderByField,
        $orderByDirection
    ) {
        return [
            'page' => $page,
            'limit' => $limit,
            'orderByField' => $orderByField,
            'orderByDirection' => $orderByDirection,
        ];
    }

    /**
     * Edit and Saves coupon_campaign.
     *
     * @param FormInterface           $form           Form
     * @param CouponCampaignInterface $couponCampaign CouponCampaign
     * @param bool                    $isValid        Is valid
     * @param Request                 $request        Request
     *
     * @return RedirectResponse Redirect response
     *
     * @Route(
     *      path = "/{id}",
     *      name = "admin_coupon_campaign_edit",
     *      requirements = {
     *          "id" = "\d+",
     *      },
     *      methods = {"GET"}
     * )
     * @Route(
     *      path = "/{id}/update",
     *      name = "admin_coupon_campaign_update",
     *      requirements = {
     *          "id" = "\d+",
     *      },
     *      methods = {"POST"}
     * )
     *
     * @Route(
     *      path = "/new",
     *      name = "admin_coupon_campaign_new",
     *      methods = {"GET"}
     * )
     * @Route(
     *      path = "/new/update",
     *      name = "admin_coupon_campaign_save",
     *      methods = {"POST"}
     * )
     *
     * @EntityAnnotation(
     *      class = {
     *          "factory" = "elcodi.factory.coupon_campaign",
     *          "method" = "create",
     *          "static" = false
     *      },
     *      mapping = {
     *          "id" = "~id~"
     *      },
     *      mappingFallback = true,
     *      name = "couponCampaign",
     *      persist = true
     * )
     * @FormAnnotation(
     *      class = "elcodi_admin_coupon_campaign_form_type_coupon_campaign",
     *      name  = "form",
     *      entity = "couponCampaign",
     *      handleRequest = true,
     *      validate = "isValid"
     * )
     *
     * @Template
     */
    public function editAction(
        FormInterface $form,
        CouponCampaignInterface $couponCampaign,
        $isValid,
        Request $request
    ) {
        if ($isValid) {
            $this->flush($couponCampaign);

            $this->addFlash('success', 'admin.coupon_campaign.saved');

            if ($request->query->get('modal', false)) {
                $redirection = $this
                    ->redirectToRoute(
                        'admin_coupon_campaign_edit',
                        ['id' => $couponCampaign->getId()]
                    );
            } else {
                $redirection = $this->redirectToRoute('admin_coupon_campaign_list');
            }

            return $redirection;
        }

        return [
            'couponCampaign' => $couponCampaign,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete entity.
     *
     * @param Request $request      Request
     * @param mixed   $entity       Entity to delete
     * @param string  $redirectPath Redirect path
     *
     * @return RedirectResponse Redirect response
     *
     * @Route(
     *      path = "/{id}/delete",
     *      name = "admin_coupon_campaign_delete"
     * )
     * @Method({"GET", "POST"})
     *
     * @EntityAnnotation(
     *      class = "elcodi.entity.coupon_campaign.class",
     *      mapping = {
     *          "id" = "~id~"
     *      }
     * )
     */
    public function deleteAction(
        Request $request,
        $entity,
        $redirectPath = null
    ) {
        return parent::deleteAction(
            $request,
            $entity,
            $this->generateUrl('admin_coupon_campaign_list')
        );
    }
}
