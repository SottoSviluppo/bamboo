<?php

namespace Elcodi\Admin\UserBundle\Controller\Component;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Product\Entity\Interfaces\CategoryInterface;
use Elcodi\Component\User\Entity\Interfaces\CustomerCategoryInterface;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Form as FormAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\JsonResponse;
use Mmoreram\ControllerExtraBundle\Annotation\Paginator as PaginatorAnnotation;
use Mmoreram\ControllerExtraBundle\ValueObject\PaginatorAttributes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CustomerCategoryComponentController
 *
 * @Route(
 *      path = "",
 * )
 */
class CustomerCategoryComponentController extends AbstractAdminController {
	/**
	 * Component for entity list.
	 *
	 * As a component, this action should not return all the html macro, but
	 * only the specific component
	 *
	 * @return array Result
	 *
	 * @Route(
	 *      path = "/user/categories/component/{page}/{limit}/{orderByField}/{orderByDirection}",
	 *      name = "admin_customer_category_list_component",
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
	 * @Template("AdminUserBundle:CustomerCategory:listComponent.html.twig")
	 *
	 * @PaginatorAnnotation(
	 *      attributes = "paginatorAttributes",
	 *      class = "elcodi.entity.customer_category.class",
	 *      page = "~page~",
	 *      limit = "~limit~",
	 *      orderBy = {
	 *          {"x", "~orderByField~", "~orderByDirection~"}
	 *      }
	 * )
	 *
	 */
	public function listComponentAction(
		Paginator $paginator,
		PaginatorAttributes $paginatorAttributes,
		$page,
		$limit,
		$orderByField,
		$orderByDirection) {

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
	 * @param FormView          $formView Form view
	 * @param CategoryInterface $category Category
	 *
	 * @return array Result
	 *
	 * @Route(
	 *      path = "/user/category/{id}/component",
	 *      name = "admin_customer_category_edit_component",
	 *      requirements = {
	 *          "id" = "\d+",
	 *      }
	 * )
	 * @Route(
	 *      path = "/user/category/new/component",
	 *      name = "admin_customer_category_new_component",
	 *      methods = {"GET"}
	 * )
	 * @Template("AdminUserBundle:CustomerCategory:editComponent.html.twig")
	 * @Method({"GET"})
	 *
	 * @EntityAnnotation(
	 *      class = {
	 *          "factory" = "elcodi.factory.customer_category",
	 *          "method" = "create",
	 *          "static" = false
	 *      },
	 *      name = "customerCategory",
	 *      mapping = {
	 *          "id" = "~id~"
	 *      },
	 *      mappingFallback = true,
	 *      persist = true
	 * )
	 * @FormAnnotation(
	 *      class = "elcodi_admin_user_form_type_customer_category",
	 *      name  = "formView",
	 *      entity = "customerCategory",
	 *      handleRequest = true,
	 *      validate = "isValid"
	 * )
	 */
	public function editComponentAction(
		FormView $formView,
		CustomerCategoryInterface $customerCategory
	) {
		return [
			'customerCategory' => $customerCategory,
			'form' => $formView,
		];
	}

	
}
