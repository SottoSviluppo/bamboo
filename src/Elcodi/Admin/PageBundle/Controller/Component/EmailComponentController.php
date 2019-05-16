<?php

namespace Elcodi\Admin\PageBundle\Controller\Component;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Page\Entity\Interfaces\PageInterface;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Form as FormAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Paginator as PaginatorAnnotation;
use Mmoreram\ControllerExtraBundle\ValueObject\PaginatorAttributes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormView;

/**
 * Class PageComponentController
 *
 * @Route(
 *      path = "/email"
 * )
 */
class EmailComponentController extends AbstractAdminController {
	/**
	 * Component for entity list.
	 *
	 * As a component, this action should not return all the html macro, but
	 * only the specific component
	 *
	 * @param Paginator           $paginator           Paginator instance
	 * @param PaginatorAttributes $paginatorAttributes Paginator attributes
	 * @param integer             $page                Page
	 * @param integer             $limit               Limit of items per page
	 * @param string              $orderByField        Field to order by
	 * @param string              $orderByDirection    Direction to order by
	 *
	 * @return array Result
	 *
	 * @Route(
	 *      path = "s/component/{page}/{limit}/{orderByField}/{orderByDirection}",
	 *      name = "admin_email_list_component",
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
	 * @Template("AdminPageBundle:Email:listComponent.html.twig")
	 *
	 * @PaginatorAnnotation(
	 *      attributes = "paginatorAttributes",
	 *      class = "elcodi.entity.page.class",
	 *      page = "~page~",
	 *      limit = "~limit~",
	 *      wheres = {
	 *          {"x", "type", "=", \Elcodi\Component\Page\ElcodiPageTypes::TYPE_EMAIL}
	 *      },
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
	 * New element component action
	 *
	 * As a component, this action should not return all the html macro, but
	 * only the specific component
	 *
	 * @param FormView      $formView Form view
	 * @param PageInterface $email Page
	 *
	 * @return array Result
	 *
	 * @Route(
	 *      path = "/{id}/component",
	 *      name = "admin_email_edit_component",
	 *      requirements = {
	 *          "id" = "\d+",
	 *      },
	 *      methods = {"GET"}
	 * )
	 * @Route(
	 *      path = "/new/component",
	 *      name = "admin_email_new_component",
	 *      methods = {"GET"}
	 * )
	 * @Template("AdminPageBundle:Email:editComponent.html.twig")
	 *
	 * @EntityAnnotation(
	 *      class = {
	 *          "factory" = "elcodi.factory.page",
	 *          "method" = "create",
	 *          "static" = false
	 *      },
	 *      name = "email",
	 *      mapping = {
	 *          "id" = "~id~"
	 *      },
	 *      mappingFallback = true,
	 * )
	 * @FormAnnotation(
	 *      class = "elcodi_admin_page_form_type_email",
	 *      name  = "formView",
	 *      entity = "email",
	 *      handleRequest = true,
	 *      validate = "isValid"
	 * )
	 */
	public function editComponentAction(
		FormView $formView,
		PageInterface $email
	) {
		return [
			'email' => $email,
			'form' => $formView,
		];
	}
}
