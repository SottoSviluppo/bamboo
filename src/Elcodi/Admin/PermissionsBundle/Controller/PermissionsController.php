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
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

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
        if (!$this->canRead()) {
            $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
            return $this->redirect($this->generateUrl('admin_homepage'));
        }
        
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
     *      entity = "permissionGroup",
     *      handleRequest = true,
     *      validate = "isValid"
     * )
    */
    public function editAction(
        FormInterface $form,
        AbstractPermissionGroupInterface $permissionGroup,
        $isValid
    ) {
        if ($permissionGroup->getId()) {
            if (!$this->canUpdate()) {
                $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
                return $this->redirect($this->generateUrl('admin_homepage'));
            }
        } else {
            if (!$this->canUpdate()) {
                $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
                return $this->redirect($this->generateUrl('admin_homepage'));
            }
        }

        if ($isValid) {
            $permissions = $permissionGroup->getPermissions();
            $permissionGroup->setPermissions($permissions);

            foreach ($permissionGroup->getPermissions() as $p) {
                if (!$p->getCanRead() && !$p->getCanCreate() && !$p->getCanUpdate() && !$p->getCanDelete()) {
                    $permissionGroup->removePermission($p);
                }
            }

            $this->flush($permissionGroup);
            $this->flushRedisCache();

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
        if (!$this->canDelete()) {
            $this->addFlash('error', $this->get('translator')->trans('admin.permissions.error'));
            return $this->redirect($this->generateUrl('admin_homepage'));
        }
        
        $view = parent::deleteAction(
            $request,
            $entity,
            $this->generateUrl('admin_permissions_list')
        );

        $this->flushRedisCache();

        return $view;
    }

    private function flushRedisCache()
    {
        try {
            $kernel = $this->get('kernel');
            $application = new Application($kernel);
            $application->setAutoExit(false);

            $input = new ArrayInput(array(
                'command' => 'redis:flushall',
                '--client' => 'metric',
                '-n' => true
            ));

            $output = new BufferedOutput();
            $application->run($input, $output);

            $content = $output->fetch();

        } catch (\Exception $e) {
            $msg = $e->getMessage();
        }
    }
}