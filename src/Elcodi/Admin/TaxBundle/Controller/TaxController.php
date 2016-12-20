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

namespace Elcodi\Admin\TaxBundle\Controller;

use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Store\Entity\Interfaces\StoreInterface;
use Elcodi\Component\Tax\Entity\Interfaces\TaxInterface;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Form as FormAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class Controller for Tax
 *
 * @Route(
 *      path = "/tax",
 * )
 */
class TaxController extends AbstractAdminController
{

    /**
     * List elements of certain entity type.
     *
     * This action is just a wrapper, so should never get any data,
     * as this is component responsibility
     *
     * @return array Result
     *
     * @Route(
     *      path = "es/list",
     *      name = "admin_tax_list"
     * )
     * @Template
     * @Method({"GET"})
     */
    public function listAction()
    {
        if (!$this->canRead()) {
            throw $this->createAccessDeniedException();
        }

        return [];
    }

    /**
     * Edit and Saves tax
     *
     * @param FormInterface         $form         Form
     * @param TaxInterface $tax Tax
     * @param boolean               $isValid      Is valid
     * @param Request               $request      Request
     *
     * @return RedirectResponse Redirect response
     *
     * @Route(
     *      path = "/{id}",
     *      name = "admin_tax_edit",
     *      requirements = {
     *          "id" = "\d+",
     *      },
     *      methods = {"GET"}
     * )
     * @Route(
     *      path = "/{id}/update",
     *      name = "admin_tax_update",
     *      requirements = {
     *          "id" = "\d+",
     *      },
     *      methods = {"POST"}
     * )
     *
     * @Route(
     *      path = "/new",
     *      name = "admin_tax_new",
     *      methods = {"GET"}
     * )
     * @Route(
     *      path = "/new/update",
     *      name = "admin_tax_save",
     *      methods = {"POST"}
     * )
     *
     * @EntityAnnotation(
     *      class = {
     *          "factory" = "elcodi.factory.tax",
     *          "method" = "create",
     *          "static" = false
     *      },
     *      mapping = {
     *          "id" = "~id~"
     *      },
     *      mappingFallback = true,
     *      name = "tax",
     *      persist = true
     * )
     * @FormAnnotation(
     *      class = "elcodi_admin_tax_form_type_tax",
     *      name  = "form",
     *      entity = "tax",
     *      handleRequest = true,
     *      validate = "isValid"
     * )
     *
     * @Template
     */
    public function editAction(
        FormInterface $form,
        TaxInterface $tax,
        $isValid,
        Request $request
    ) {
        if ($tax->getId()) {
            if (!$this->canUpdate()) {
                throw $this->createAccessDeniedException();
            }
        } else {
            if (!$this->canCreate()) {
                throw $this->createAccessDeniedException();
            }
        }

        if ($isValid) {

            $this->flush($tax);

            $this->addFlash('success', 'admin.tax.saved');

            if ($request->query->get('modal', false)) {
                $redirection = $this
                    ->redirectToRoute(
                        'admin_tax_edit',
                        ['id' => $tax->getId()]
                    );
            } else {
                $redirection = $this->redirectToRoute('admin_tax_list');
            }

            return $redirection;
        }

        return [
            'tax' => $tax,
            'form' => $form->createView(),
        ];
    }

    /**
     * Enable entity
     *
     * @param TaxInterface $tax The tax to enable
     *
     * @return array Result
     *
     * @Route(
     *      path = "/{id}/enable",
     *      name = "admin_tax_enable"
     * )
     * @Method({"POST"})
     *
     * @EntityAnnotation(
     *      class = "elcodi.entity.tax.class",
     *      name = "tax",
     *      mapping = {
     *          "id" = "~id~"
     *      }
     * )
     *
     * @JsonResponse()
     */
    public function enableTaxAction(
        TaxInterface $tax
    ) {
        if (!$this->canUpdate()) {
            throw $this->createAccessDeniedException();
        }

        $translator = $this->get('translator');

        $this->enableEntity($tax);
        $this->flushCache();

        return ['message' => $translator->trans('admin.tax.saved.enabled')];
    }

    /**
     * Disable entity
     *
     * @param TaxInterface $tax The tax to disable
     *
     * @return array Result
     *
     * @Route(
     *      path = "/{id}/disable",
     *      name = "admin_tax_disable"
     * )
     * @Method({"POST"})
     *
     * @EntityAnnotation(
     *      class = "elcodi.entity.tax.class",
     *      name = "tax",
     *      mapping = {
     *          "id" = "~id~"
     *      }
     * )
     *
     * @JsonResponse()
     */
    public function disableTaxAction(
        TaxInterface $tax
    ) {
        if (!$this->canUpdate()) {
            throw $this->createAccessDeniedException();
        }

        $translator = $this->get('translator');

        /**
         * We cannot disable the default locale
         */
        $masterTax = $configManager = $this
            ->get('elcodi.store')
            ->getDefaultTax();

        if ($tax->getId() == $masterTax) {
            throw new HttpException(
                '403',
                $translator->trans('admin.tax.error.disable_master_tax')
            );
        }

        $this->disableEntity($tax);
        $this->flushCache();

        return ['message' => $translator->trans('admin.tax.saved.disabled')];
    }

    /**
     * Set the master tax.
     *
     * @param TaxInterface $tax
     *
     * @return array
     *
     * @Route(
     *      path = "/{id}/master",
     *      name = "admin_tax_master"
     * )
     * @Method({"POST"})
     *
     * @EntityAnnotation(
     *      class = {
     *          "factory" = "elcodi.wrapper.store",
     *          "method" = "get",
     *          "static" = false
     *      },
     *      name = "store",
     *      persist = false
     * )
     * @EntityAnnotation(
     *      class = "elcodi.entity.tax.class",
     *      name = "tax",
     *      mapping = {
     *          "id" = "~id~"
     *      }
     * )
     *
     * @JsonResponse()
     */
    public function masterTaxAction(
        StoreInterface $store,
        TaxInterface $tax
    ) {
        if (!$this->canUpdate()) {
            throw $this->createAccessDeniedException();
        }

        $translator = $this->get('translator');
        if (!$tax->isEnabled()) {
            throw new HttpException(
                '403',
                $translator->trans('admin.tax.error.setting_disabled_master_tax')
            );
        }

        $store->setDefaultTax($tax);
        $this
            ->get('elcodi.object_manager.store')
            ->flush($store);
        $this->flushCache();

        return [
            'message' => $translator->trans('admin.tax.saved.master'),
        ];
    }
}
