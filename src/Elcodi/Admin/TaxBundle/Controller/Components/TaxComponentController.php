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

namespace Elcodi\Admin\TaxBundle\Controller\Components;

use Doctrine\ORM\Tools\Pagination\Paginator;

use Mmoreram\ControllerExtraBundle\Annotation\Entity as EntityAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Form as FormAnnotation;
use Mmoreram\ControllerExtraBundle\Annotation\Paginator as PaginatorAnnotation;
use Mmoreram\ControllerExtraBundle\ValueObject\PaginatorAttributes;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormView;

use Elcodi\Admin\CoreBundle\Controller\Abstracts\AbstractAdminController;

use Elcodi\Component\Tax\Entity\Interfaces\TaxInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class TaxComponentController
 *
 * @Route(
 *      path = "/tax",
 * )
 */
class TaxComponentController extends AbstractAdminController
{
    /**
     * Component for entity list.
     *
     * As a component, this action should not return all the html macro, but
     * only the specific component
     *
     * @return array Result
     *
     * @Route(
     *      path = "s/component",
     *      name = "admin_tax_list_component",
     * )
     * @Template("AdminTaxBundle:Tax:listComponent.html.twig")
     * @Method({"GET"})
     */
    public function listComponentAction()
    {
        $taxes = $this
            ->get('elcodi.repository.tax')
            ->findBy(
                [],
                [
                    'enabled' => 'DESC',
                ]
            );

        return [
            'paginator' => $taxes,
        ];
    }


    /**
     * New element component action
     *
     * As a component, this action should not return all the html macro, but
     * only the specific component
     *
     * @param FormView              $formView     Form view
     * @param TaxInterface $tax Tax
     *
     * @return array Result
     *
     * @Route(
     *      path = "/{id}/component",
     *      name = "admin_tax_edit_component",
     *      requirements = {
     *          "id" = "\d+",
     *      }
     * )
     * @Route(
     *      path = "/new/component",
     *      name = "admin_tax_new_component",
     *      methods = {"GET"}
     * )
     * @Template("AdminTaxBundle:Tax:editComponent.html.twig")
     * @Method({"GET"})
     *
     * @EntityAnnotation(
     *      class = {
     *          "factory" = "elcodi.factory.tax",
     *          "method" = "create",
     *          "static" = false
     *      },
     *      name = "tax",
     *      mapping = {
     *          "id" = "~id~"
     *      },
     *      mappingFallback = true,
     *      persist = true
     * )
     * @FormAnnotation(
     *      class = "elcodi_admin_tax_form_type_tax",
     *      name  = "form",
     *      entity = "tax",
     *      handleRequest = true,
     *      validate = "isValid"
     * )
     */
    public function editComponentAction(
        Form $form,
        TaxInterface $tax,
        Request $request
    ) {
        return [
            'tax' => $tax,
            'form'     => $form->createView(),
        ];
    }
}
