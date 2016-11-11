<?php

namespace Elcodi\Admin\PermissionsBundle\Controller;

use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Form as FormAnnotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;
use Elcodi\Component\Permissions\Entity\Interfaces\AbstractPermissionInterface;
use Elcodi\Component\Permissions\Entity\Interfaces\AbstractPermissionGroupInterface;

class PermissionsController extends AbstractAdminController
{
    /**
    * @Template
    * @Method({"GET"})
    */
    public function listAction(
        $page,
        $limit,
        $orderByField,
        $orderByDirection  
    ) {
        return [
            'page'             => $page,
            'limit'            => $limit,
            'orderByField'     => $orderByField,
            'orderByDirection' => $orderByDirection,
        ];
    }

    /**
     * @Template
     * @EntityAnnotation(
     *      class = {
     *          "factory" = "elcodi.factory.permission_group",
     *      },
     *      mapping = {
     *          "id" = "~id~"
     *      },
     *      mappingFallback = true,
     *      name = "permissionGroup",
     *      persist = true
     * )
     * @FormAnnotation(
     *      class = "elcodi_admin_permissions_form_type_permission_group",
     *      name  = "form",
     *      entity = "permission_group",
     *      handleRequest = true,
     *      validate = "isValid"
     * )
    */
    public function editAction(
        FormInterface $form,
        AbstractPermissionGroupInterface $permissionGroup,
        $isValid
    ) {
        if ($isValid) {
            $this->flush($permissionGroup);

            $this->addFlash('success', 'admin.permissions.saved');

            return $this->redirectToRoute('admin_permissions_list');
        }

        return [
            'permissionGroup' => $permissionGroup,
            'form' => $form->createView(),
        ];
    }

    /**
     * @EntityAnnotation(
     *      class = "elcodi.entity.permission_group.class",
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
            $this->generateUrl('admin_permissions_list')
        );
    }
}