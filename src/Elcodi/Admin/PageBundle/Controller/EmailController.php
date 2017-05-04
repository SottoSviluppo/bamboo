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

namespace Elcodi\Admin\PageBundle\Controller;

use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Page\Entity\Interfaces\PageInterface;
use Elcodi\Component\User\Entity\Customer;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Form as FormAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Controller for Email
 *
 * @Route(
 *      path = "/email",
 * )
 */
class EmailController extends AbstractAdminController
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
     *      path = "s/",
     *      name = "admin_email_list",
     *      methods = {"GET"},
     * )
     * @Template
     */
    public function listAction()
    {
        if (!$this->canRead()) {
            $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
            return $this->redirect($this->generateUrl('admin_homepage'));
        }

        return [];
    }

    /**
     * Edit and Saves page
     *
     * @param FormInterface $form    Form
     * @param PageInterface $email   Email
     * @param boolean       $isValid Is valid
     *
     * @return RedirectResponse Redirect response
     *
     * @Route(
     *      path = "/{id}",
     *      name = "admin_email_edit",
     *      requirements = {
     *          "id" = "\d+",
     *      },
     *      methods = {"GET"}
     * )
     * @Route(
     *      path = "/{id}/update",
     *      name = "admin_email_update",
     *      requirements = {
     *          "id" = "\d+",
     *      },
     *      methods = {"POST"}
     * )
     *
     * @EntityAnnotation(
     *      class = {
     *          "factory" = "elcodi.factory.page",
     *          "method" = "create",
     *          "static" = false
     *      },
     *      mapping = {
     *          "id" = "~id~",
     *          "type" = \Elcodi\Component\Page\ElcodiPageTypes::TYPE_EMAIL,
     *      },
     *      name = "email",
     *      persist = true
     * )
     * @FormAnnotation(
     *      class = "elcodi_admin_page_form_type_email",
     *      name  = "form",
     *      entity = "email",
     *      handleRequest = true,
     *      validate = "isValid"
     * )
     *
     * @Template
     */
    public function editAction(
        FormInterface $form,
        PageInterface $email,
        $isValid
    ) {
        if ($email->getId()) {
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

        if ($isValid) {
            $this->flush($email);

            $this->addFlash('success', 'admin.mailing.saved');

            return $this->redirectToRoute('admin_email_list');
        }

        return [
            'email' => $email,
            'form' => $form->createView(),
        ];
    }

    /**
     * Edit and Saves page
     *
     * @param FormInterface $form    Form
     * @param PageInterface $email   Email
     * @param boolean       $isValid Is valid
     *
     * @return RedirectResponse Redirect response
     *
     * @Route(
     *      path = "/{id}/send-test",
     *      name = "admin_email_send",
     *      requirements = {
     *          "id" = "\d+",
     *      },
     *      methods = {"GET"}
     * )
     * @EntityAnnotation(
     *      class = {
     *          "factory" = "elcodi.factory.page",
     *          "method" = "create",
     *          "static" = false
     *      },
     *      mapping = {
     *          "id" = "~id~",
     *          "type" = \Elcodi\Component\Page\ElcodiPageTypes::TYPE_EMAIL,
     *      },
     *      name = "email",
     *      persist = true
     * )
     *
     * @Template
     */
    public function sendTestEmailAction(
        PageInterface $email
    ) {

        if ($email->getName() == "order_confirmation" || $email->getName() == "order_shipped") {
            $order = $this->get('elcodi.repository.order')->findBy(array(), array('id' => 'desc'), 1);
            $customer = $order[0]->getCustomer();
            $context = ['order' => $order[0], 'customer' => $customer];
        } else {
            $customer = new Customer();
            $customer->setFirstname('Customer');
            $customer->setEmail('customer@customer.com');
            $context = ['customer' => $customer];
        }

        $this->get('elcodi_store.mailer.sender')->send(
            $email->getName(),
            $context,
            'info@sottosviluppo.com',
            false
        );

        return $this->redirectToRoute('admin_email_list');
    }

    /**
     * @Route(
     *      path = "/{id}/send-order-confirmation",
     *      name = "admin_email_send_order_confirmation",
     *      requirements = {
     *          "id" = "\d+",
     *      },
     *      methods = {"GET"}
     * )
     */
    public function sendOrderConfirmationAction(
        $id
    ) {
        $email = $this->get('elcodi.repository.page')->findOneByName('order_confirmation');
        $order = $this->get('elcodi.repository.order')->find($id);
        $customer = $order->getCustomer();
        $context = ['order' => $order, 'customer' => $customer];

        $this->get('elcodi_store.mailer.sender')->send(
            $email->getName(),
            $context,
            'info@sottosviluppo.com',
            false
        );

        return $this->redirectToRoute('admin_order_edit', ['id' => $id]);
    }
}
