<?php

namespace Elcodi\Admin\UserBundle\Controller;

use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\User\Entity\Interfaces\CustomerCategoryInterface;
use Elcodi\Component\User\Entity\Interfaces\CustomerInterface;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Form as FormAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Controller for Customer
 *
 * @Route(
 *      path = "/user/category/customer",
 * )
 */
class CustomerCategoryController extends AbstractAdminController {
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
	 *      name = "admin_customer_category_list",
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
	 * Edit and Saves customer
	 *
	 * @param FormInterface     $form     Form
	 * @param CustomerInterface $customer Customer
	 * @param boolean           $isValid  Is valid
	 *
	 * @return RedirectResponse Redirect response
	 *
	 * @Route(
	 *      path = "/{id}",
	 *      name = "admin_category_customer_view",
	 *      requirements = {
	 *          "id" = "\d+",
	 *      },
	 *      methods = {"GET"}
	 * )
	 * @Route(
	 *      path = "/{id}/edit",
	 *      name = "admin_category_customer_edit",
	 *      requirements = {
	 *          "id" = "\d+",
	 *      },
	 *      methods = {"GET"}
	 * )
	 * @Route(
	 *      path = "/{id}/update",
	 *      name = "admin_category_customer_update",
	 *      requirements = {
	 *          "id" = "\d+",
	 *      },
	 *      methods = {"POST"}
	 * )
	 *
	 * @Route(
	 *      path = "/new",
	 *      name = "admin_customer_category_new",
	 *      methods = {"GET"}
	 * )
	 * @Route(
	 *      path = "/new/update",
	 *      name = "admin_category_customer_save",
	 *      methods = {"POST"}
	 * )
	 *
	 * @EntityAnnotation(
	 *      class = {
	 *          "factory" = "elcodi.factory.customer_category",
	 *          "method" = "create",
	 *          "static" = false
	 *      },
	 *      mapping = {
	 *          "id" = "~id~"
	 *      },
	 *      mappingFallback = true,
	 *      name = "customerCategory",
	 *      persist = true
	 * )
	 * @FormAnnotation(
	 *      class = "elcodi_admin_user_form_type_customer_category",
	 *      name  = "form",
	 *      entity = "customerCategory",
	 *      handleRequest = true,
	 *      validate = "isValid"
	 * )
	 *
	 * @Template
	 */
	public function editAction(
		FormInterface $form,
		CustomerCategoryInterface $customerCategory,
		$isValid
	) {

		if ($customerCategory->getId()) {
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
		if ($form->getErrorsAsString() == "") {
			if ($isValid) {
				$this->manageExtraFields($customerCategory);
				$this->flush($customerCategory);

				$this->addFlash(
					'success',
					$this
						->get('translator')
						->trans('admin.customer.saved')
				);

				return $this->redirectToRoute('admin_customer_category_list');
			}
		} else {
			$this->addFlash(
				'error',
				$form->getErrorsAsString()
			);
		}

		return [
			'customerCategory' => $customerCategory,
			'form' => $form->createView(),
		];
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
	 *      name = "admin_customer_category_delete",
	 *      methods = {"GET", "POST"}
	 * )
	 *
	 * @EntityAnnotation(
	 *      class = "elcodi.entity.customer_category.class",
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
			$this->generateUrl('admin_customer_category_list')
		);
	}
}
