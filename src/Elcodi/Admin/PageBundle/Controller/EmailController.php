<?php

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
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Controller for Blog Post
 *
 * @Route(
 *      path = "/email",
 * )
 */
class EmailController extends AbstractAdminController {
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
	public function listAction() {
		if (!$this->canRead()) {
			$this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
			return $this->redirect($this->generateUrl('admin_homepage'));
		}

		return [];
	}

	/**
	 * Edit and Saves page
	 *
	 * @param FormInterface $form     Form
	 * @param PageInterface $blogPost Page
	 * @param boolean       $isValid  Is valid
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
	 * @Route(
	 *      path = "/new",
	 *      name = "admin_email_new",
	 *      methods = {"GET"}
	 * )
	 * @Route(
	 *      path = "/save",
	 *      name = "admin_email_save",
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
	 *          "id" = "~id~"
	 *      },
	 *      mappingFallback = true,
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

			$this->addFlash(
				'success',
				$this
					->get('translator')
					->trans('admin.email.saved')
			);

			return $this->redirectToRoute('admin_email_list');
		}

		return [
			'email' => $email,
			'form' => $form->createView(),
		];
	}

	/**
	 * Delete a blog post
	 *
	 * @param Request $request      Request
	 * @param mixed   $entity       Entity to delete
	 * @param string  $redirectPath Redirect path
	 *
	 * @return RedirectResponse Redirect response
	 *
	 * @Route(
	 *      path = "/{id}/delete",
	 *      name = "admin_email_delete",
	 *      methods = {"GET", "POST"}
	 * )
	 *
	 * @EntityAnnotation(
	 *      class = "elcodi.entity.page.class",
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
			$this->generateUrl('admin_email_list')
		);
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
}
