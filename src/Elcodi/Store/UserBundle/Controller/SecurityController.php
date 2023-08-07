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

namespace Elcodi\Store\UserBundle\Controller;

use Elcodi\Component\User\Entity\Interfaces\CustomerInterface;
use Elcodi\Store\CoreBundle\Controller\Traits\TemplateRenderTrait;
use Mmoreram\ControllerExtraBundle\Annotation\Entity;
use Mmoreram\ControllerExtraBundle\Annotation\Form as AnnotationForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

/**
 * Class SecurityController
 */
class SecurityController extends Controller
{
    use TemplateRenderTrait;

    /**
     * Login page
     *
     * @param FormView $loginFormView Login form view
     *
     * @return Response Response
     *
     * @Route(
     *      path = "/login",
     *      name = "store_login",
     *      methods = {"GET"}
     * )
     *
     * @AnnotationForm(
     *      class = "store_user_form_type_login",
     *      name  = "loginFormView"
     * )
     */
    public function loginAction(FormView $loginFormView)
    {
        $translator = $this->get('translator');

        /**
         * If user is already logged, go to redirect url
         */
        $authorizationChecker = $this->get('security.authorization_checker');
        if ($authorizationChecker->isGranted('ROLE_CUSTOMER')) {
            return $this->redirectToRoute('store_homepage');
        }

        /**
         * Checking for authentication errors in session
         */
        $session = $this->get('session');
        if ($session->has(Security::AUTHENTICATION_ERROR)) {
            $message = $translator
                ->trans('store.login.wrong_combination');

            $this
                ->addFlash('error', $message);
        }

        return $this->renderTemplate(
            'Pages:user-login.html.twig',
            [
                'form' => $loginFormView,
            ]
        );
    }

    /**
     * Register page
     *
     * @param CustomerInterface $customer empty customer
     * @param FormView $registerFormView Register form type
     * @param boolean $isValid Form submition is valid
     *
     * @return Response Response
     *
     * @Route(
     *      path = "/register",
     *      name = "store_register",
     *      methods = {"GET", "POST"}
     * )
     *
     * @Entity(
     *      name     = "customer",
     *      class    = {
     *          "factory"  = "elcodi.factory.customer"
     *      },
     *      persist  = false
     * )
     * @AnnotationForm(
     *      class         = "store_user_form_type_register",
     *      entity        = "customer",
     *      handleRequest = true,
     *      name          = "registerFormView",
     *      validate      = "isValid"
     * )
     */
    public function registerAction(
        CustomerInterface $customer,
        FormView          $registerFormView,
                          $isValid
    )
    {
        return $this->register($customer, $registerFormView, $isValid, 'Pages:user-register.html.twig');
    }

    /**
     * Register page
     *
     * @param CustomerInterface $customer empty customer
     * @param FormView $registerFormView Register form type
     * @param boolean $isValid Form submition is valid
     *
     * @return Response Response
     *
     * @Route(
     *      path = "/register_block",
     *      name = "store_register_block",
     *      methods = {"GET", "POST"}
     * )
     *
     * @Entity(
     *      name     = "customer",
     *      class    = {
     *          "factory"  = "elcodi.factory.customer"
     *      },
     *      persist  = false
     * )
     * @AnnotationForm(
     *      class         = "store_user_form_type_register",
     *      entity        = "customer",
     *      handleRequest = true,
     *      name          = "registerFormView",
     *      validate      = "isValid"
     * )
     */
    public function registerBlockAction(
        CustomerInterface $customer,
        FormView          $registerFormView,
                          $isValid
    )
    {
        /**
         * If user is already logged, go to redirect url
         */
        $authorizationChecker = $this->get('security.authorization_checker');
        if ($authorizationChecker->isGranted('ROLE_CUSTOMER')) {
            return $this->getJson(array(
                'success' => true,
                'message' => 'Utente già loggato',
            ));
        }

        if ($isValid) {
            $customerManager = $this->get('elcodi.object_manager.customer');
            $customerManager->persist($customer);
            $customerManager->flush($customer);

            $this
                ->get('elcodi.manager.customer')
                ->register($customer);

            return $this->getJson(array(
                'success' => true,
                'message' => 'Utente registrato con successo',
            ));
        }
        $request = $this->get('request');
        if ($request->isXmlHttpRequest()) {
            return $this->getJson(array(
                'success' => false,
                'message' => 'Errore generico',
            ));
        } else {
            return $this->renderTemplate(
                'Pages:user-register-block.html.twig',
                [
                    'form' => $registerFormView,
                ]
            );
        }

    }

    protected function getJson($array)
    {
        $response = new \Symfony\Component\HttpFoundation\JsonResponse($array);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        return $response;
    }

    /**
     * Register page full
     *
     * @param CustomerInterface $customer empty customer
     * @param FormView $registerFormView Register form type
     * @param boolean $isValid Form submition is valid
     *
     * @return Response Response
     *
     * @Route(
     *      path = "/register-full",
     *      name = "store_register_full",
     *      methods = {"GET", "POST"}
     * )
     *
     * @Entity(
     *      name     = "customer",
     *      class    = {
     *          "factory"  = "elcodi.factory.customer"
     *      },
     *      persist  = false
     * )
     * @AnnotationForm(
     *      class         = "store_user_form_type_register_full",
     *      entity        = "customer",
     *      handleRequest = true,
     *      name          = "registerFormView",
     *      validate      = "isValid"
     * )
     */
    public function registerFullAction(
        CustomerInterface $customer,
        FormView          $registerFormView,
                          $isValid
    )
    {
        return $this->register($customer, $registerFormView, $isValid, 'Pages:user-register-full.html.twig');
    }

    private function register(CustomerInterface $customer,
                              FormView          $registerFormView,
                                                $isValid,
                                                $template)
    {

        /**
         * If user is already logged, go to redirect url
         */
        $authorizationChecker = $this->get('security.authorization_checker');
        if ($authorizationChecker->isGranted('ROLE_CUSTOMER')) {
            return $this->redirectToRoute('store_homepage');
        }

        if ($isValid) {
            // superfluous checks just to make sure
            if ($this->isExistCustomer($customer->getEmail())) {
                $this->addFlash('error', 'Indirizzo email già presente');
                return $this->renderTemplate(
                    $template,
                    [
                        'form' => $registerFormView,
                    ]
                );
            }

            // superfluous checks just to make sure
            if (!$this->isCustomerValid($customer)) {
                return $this->renderTemplate(
                    $template,
                    [
                        'form' => $registerFormView,
                    ]
                );
            }

            $customerManager = $this->get('elcodi.object_manager.customer');
            $customerManager->persist($customer);
            $customerManager->flush($customer);

            $this
                ->get('elcodi.manager.customer')
                ->register($customer);

            return $this->redirectToRoute('store_homepage');
        }
        return $this->renderTemplate(
            $template,
            [
                'form' => $registerFormView,
            ]
        );
    }

    public function isCustomerValid($customer)
    {
        if (!$this->validateEmail($customer->getEmail())) {
            return false;
        }

        if (strlen(trim($customer->getPassword())) == 0) {
            return false;
        }

        return true;
    }

    public function validateEmail($email)
    {
        if (strlen(trim($email)) == 0) {
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    /**
     * Ritorna TRUE se esiste già un Customer con l'indirizzo email inserito
     * @param string $email
     * @return bool
     */
    private function isExistCustomer(string $email)
    {
        $existCustomer = $this->get('elcodi.repository.customer')->findOneBy(['email' => $email]);
        if ($existCustomer) {
            return true;
        }
        return false;
    }
}
