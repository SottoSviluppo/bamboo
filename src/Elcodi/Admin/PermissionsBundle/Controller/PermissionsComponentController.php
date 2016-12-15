<?php

namespace Elcodi\Admin\PermissionsBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Form as FormAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Paginator as PaginatorAnnotation;
use Mmoreram\ControllerExtraBundle\ValueObject\PaginatorAttributes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormView;

use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Permissions\Entity\Interfaces\AbstractPermissionInterface;
use Elcodi\Component\Permissions\Entity\Interfaces\AbstractPermissionGroupInterface;

class PermissionsComponentController extends AbstractAdminController
{
    /**
    * @Template("AdminPermissionsBundle:Permissions:listComponent.html.twig")
    * @PaginatorAnnotation(
    *      attributes = "paginatorAttributes",
    *      class = "elcodi.entity.permission_group.class",
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
            'paginator'        => $paginator,
            'page'             => $page,
            'limit'            => $limit,
            'orderByField'     => $orderByField,
            'orderByDirection' => $orderByDirection,
            'totalPages'       => $paginatorAttributes->getTotalPages(),
            'totalElements'    => $paginatorAttributes->getTotalElements(),
        ];
    }

    /**
     * @Template("AdminPermissionsBundle:Permissions:editComponent.html.twig")
     *
     * @EntityAnnotation(
     *      class = {
     *          "factory" = "elcodi.factory.permission_group",
     *      },
     *      name = "permissionGroup",
     *      mapping = {
     *          "id": "~id~",
     *      },
     *      mappingFallback = true,
     * )
     * @FormAnnotation(
     *      class = "elcodi_admin_permissions_form_type_permission_group",
     *      name  = "formView",
     *      entity = "permissionGroup"
     * )
    */
    public function editComponentAction(
        AbstractPermissionGroupInterface $permissionGroup,
        FormView $formView
    ) {
        return [
            'permissionGroup' => $permissionGroup,
            'form'   => $formView,
        ];
    }
}