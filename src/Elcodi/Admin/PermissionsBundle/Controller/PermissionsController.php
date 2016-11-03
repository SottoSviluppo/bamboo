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
    public function listAction()
    {

    }
}