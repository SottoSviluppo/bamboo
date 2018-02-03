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

namespace Elcodi\Admin\CouponBundle\Controller;

use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Core\Entity\Interfaces\EnabledInterface;
use Elcodi\Component\Coupon\Entity\Interfaces\CouponInterface;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Form as FormAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Controller for Coupon
 *
 * @Route(
 *      path = "/coupon",
 * )
 */
class CouponController extends AbstractAdminController
{
    /**
     * List elements of certain entity type.
     *
     * This action is just a wrapper, so should never get any data,
     * as this is component responsibility
     *
     * @param integer $page             Page
     * @param integer $limit            Limit of items per page
     * @param string  $orderByField     Field to order by
     * @param string  $orderByDirection Direction to order by
     *
     * @return array Result
     *
     * @Route(
     *      path = "s/{page}/{limit}/{orderByField}/{orderByDirection}",
     *      name = "admin_coupon_list",
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
     *
     * @Template
     */
    public function listAction(
        $page,
        $limit,
        $orderByField,
        $orderByDirection
    ) {
        if (!$this->canRead()) {
            $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
            return $this->redirect($this->generateUrl('admin_homepage'));
        }

        return [
            'page' => $page,
            'limit' => $limit,
            'orderByField' => $orderByField,
            'orderByDirection' => $orderByDirection,
        ];
    }

    /**
     * Edit and Saves page
     *
     * @param FormInterface   $form    Form
     * @param CouponInterface $coupon  Coupon
     * @param boolean         $isValid Request handle is valid
     *
     * @return RedirectResponse Redirect response
     *
     * @Route(
     *      path = "/{id}",
     *      name = "admin_coupon_edit",
     *      requirements = {
     *          "id" = "\d+",
     *      },
     *      methods = {"GET"}
     * )
     * @Route(
     *      path = "/{id}/update",
     *      name = "admin_coupon_update",
     *      requirements = {
     *          "id" = "\d+",
     *      },
     *      methods = {"POST"}
     * )
     *
     * @Route(
     *      path = "/new",
     *      name = "admin_coupon_new",
     *      methods = {"GET"}
     * )
     * @Route(
     *      path = "/new/update",
     *      name = "admin_coupon_save",
     *      methods = {"POST"}
     * )
     *
     * @EntityAnnotation(
     *      class = {
     *          "factory" = "elcodi.factory.coupon",
     *      },
     *      mapping = {
     *          "id" = "~id~"
     *      },
     *      mappingFallback = true,
     *      name = "coupon",
     *      persist = true
     * )
     * @FormAnnotation(
     *      class = "elcodi_admin_coupon_form_type_coupon",
     *      name  = "form",
     *      entity = "coupon",
     *      handleRequest = true,
     *      validate = "isValid"
     * )
     *
     * @Template
     */
    public function editAction(
        FormInterface $form,
        CouponInterface $coupon,
        $isValid
    ) {
        if ($coupon->getId()) {
            if (!$this->canUpdate()) {
                $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
                return $this->redirect($this->generateUrl('admin_homepage'));
            }
        } else {
            if (!$this->canCreate()) {
                $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
                return $this->redirect($this->generateUrl('admin_homepage'));
            }
        }

        // check if already exists
        if ($coupon->getId() <= 0) {
            $oldCoupon = $this->get('elcodi.repository.coupon')->findOneByCode($coupon->getCode());
            if ($oldCoupon != null) {
                $this->addFlash('error', 'admin.coupon.already_exists');
                $isValid = false;
            }
        }

        if ($isValid) {
            $this->manageExtraFields($coupon);
            $this->flush($coupon);
            $this->addFlash('success', 'admin.coupon.saved');

            return $this->redirectToRoute('admin_coupon_list');
        }

        return [
            'coupon' => $coupon,
            'form' => $form->createView(),
        ];
    }

    /**
     * Enable entity
     *
     * @param Request          $request Request
     * @param EnabledInterface $entity  Entity to enable
     *
     * @return array Result
     *
     * @Route(
     *      path = "/{id}/enable",
     *      name = "admin_coupon_enable",
     *      methods = {"GET", "POST"}
     * )
     *
     * @EntityAnnotation(
     *      class = "elcodi.entity.coupon.class",
     *      mapping = {
     *          "id" = "~id~"
     *      }
     * )
     */
    public function enableAction(
        Request $request,
        EnabledInterface $entity
    ) {
        if (!$this->canUpdate()) {
            $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
            return $this->redirect($this->generateUrl('admin_homepage'));
        }

        return parent::enableAction(
            $request,
            $entity
        );
    }

    /**
     * Disable entity
     *
     * @param Request          $request Request
     * @param EnabledInterface $entity  Entity to disable
     *
     * @return array Result
     *
     * @Route(
     *      path = "/{id}/disable",
     *      name = "admin_coupon_disable",
     *      methods = {"GET", "POST"}
     * )
     *
     * @EntityAnnotation(
     *      class = "elcodi.entity.coupon.class",
     *      mapping = {
     *          "id" = "~id~"
     *      }
     * )
     */
    public function disableAction(
        Request $request,
        EnabledInterface $entity
    ) {
        if (!$this->canUpdate()) {
            $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
            return $this->redirect($this->generateUrl('admin_homepage'));
        }

        return parent::disableAction(
            $request,
            $entity
        );
    }

    /**
     * Delete entity
     *
     * @param Request $request      Request
     * @param mixed   $entity       Entity to delete
     * @param string  $redirectPath Redirect path
     *
     * @return RedirectResponse Redirect response
     *
     * @Route(
     *      path = "/{id}/delete",
     *      name = "admin_coupon_delete",
     *      methods = {"GET", "POST"}
     * )
     *
     * @EntityAnnotation(
     *      class = "elcodi.entity.coupon.class",
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
        if (!$this->canDelete()) {
            $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
            return $this->redirect($this->generateUrl('admin_homepage'));
        }

        return parent::deleteAction(
            $request,
            $entity,
            $this->generateUrl('admin_coupon_list')
        );
    }
}
