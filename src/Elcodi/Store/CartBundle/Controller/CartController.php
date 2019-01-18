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

namespace Elcodi\Store\CartBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Elcodi\Component\Cart\Entity\Interfaces\CartInterface;
use Elcodi\Component\Cart\Entity\Interfaces\CartLineInterface;
use Elcodi\Component\Product\Entity\Interfaces\PurchasableInterface;
use Elcodi\Store\CoreBundle\Controller\Traits\TemplateRenderTrait;
use Mmoreram\ControllerExtraBundle\Annotation\Entity as AnnotationEntity;
use Mmoreram\ControllerExtraBundle\Annotation\Form as AnnotationForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cart controllers
 *
 * @Route(
 *      path = "/cart",
 * )
 */
class CartController extends Controller {
	use TemplateRenderTrait;

	/**
	 * Cart view
	 *
	 * @param FormView      $formView Form view
	 * @param CartInterface $cart     Cart
	 *
	 * @return Response Response
	 *
	 * @Route(
	 *      path = "",
	 *      name = "store_cart_view",
	 *      methods = {"GET"}
	 * )
	 *
	 * @AnnotationEntity(
	 *      class = {
	 *          "factory" = "elcodi.wrapper.cart",
	 *          "method" = "get",
	 *          "static" = false,
	 *      },
	 *      name = "cart"
	 * )
	 * @AnnotationForm(
	 *      class = "store_cart_form_type_cart",
	 *      name  = "formView",
	 *      entity = "cart",
	 * )
	 */
	public function viewAction(
		FormView $formView,
		CartInterface $cart
	) {
		$cartCoupons = $this
			->get('elcodi.manager.cart_coupon')
			->getCartCoupons($cart);

		$cartTaxAmount = 0;
		//verifico che nelle impostazioni dello store non sia specificato che i prezzi sia comprensivi delle tasse
		if (!$this->get('elcodi.store')->getTaxIncluded()) {
			$cartTaxAmount = $this->get('elcodi.loader.cart_prices')->getTaxAmount($cart);
			if (count($cartCoupons) > 0) {
				$cartTaxAmount = $this->get('elcodi.loader.cart_prices')->getTaxAmountWithCoupon($cart);
			}
			$cart->setTaxAmount($cartTaxAmount);
			$this
				->get('elcodi.object_manager.cart')
				->flush($cart);
		}
		return $this->renderTemplate(
			'Pages:cart-view.html.twig',
			[
				'cart' => $cart,
				'cartCoupons' => $cartCoupons,
				'form' => $formView,
			]
		);
	}

	/**
	 * Adds product into cart
	 *
	 * @param Request       $request Request object
	 * @param CartInterface $cart    Cart
	 * @param integer       $id      Purchasable Id
	 *
	 * @return Response Redirect response
	 *
	 * @throws EntityNotFoundException Purchasable not found
	 *
	 * @Route(
	 *      path = "/purchasable/{id}/add",
	 *      name = "store_cart_add_purchasable",
	 *      requirements = {
	 *          "id": "\d+"
	 *      },
	 *      methods = {"GET", "POST"}
	 * )
	 *
	 * @AnnotationEntity(
	 *      class = {
	 *          "factory" = "elcodi.wrapper.cart",
	 *          "method" = "get",
	 *          "static" = false,
	 *      },
	 *      name = "cart"
	 * )
	 */
	public function addPurchasableAction(
		Request $request,
		CartInterface $cart,
		$id
	) {
		$purchasable = $this
			->get('elcodi.repository.purchasable')
			->find($id);
		if (!$purchasable instanceof PurchasableInterface) {
			throw new EntityNotFoundException('Purchasable not found');
		}

		$cartQuantity = (int) $request
			->request
			->get('add-cart-quantity', 1);

		$this
			->get('elcodi.manager.cart')
			->addPurchasable(
				$cart,
				$purchasable,
				$cartQuantity
			);

		return $this->redirect(
			$this->generateUrl('store_cart_view')
		);
	}

	/**
	 * Adds multiple product into cart
	 *
	 * @param Request       $request Request object
	 * @param CartInterface $cart    Cart
	 * @param integer       $id      Purchasable Id
	 *
	 * @return Response Redirect response
	 *
	 * @throws EntityNotFoundException Purchasable not found
	 *
	 * @Route(
	 *      path = "/purchasable/add-multiple",
	 *      name = "store_cart_add_multiple_purchasable",
	 *      methods = {"GET", "POST"}
	 * )
	 *
	 * @AnnotationEntity(
	 *      class = {
	 *          "factory" = "elcodi.wrapper.cart",
	 *          "method" = "get",
	 *          "static" = false,
	 *      },
	 *      name = "cart"
	 * )
	 */
	public function addMultiplePurchasableAction(
		Request $request,
		CartInterface $cart
	) {
		$lines = $request->get('lines');
		foreach ($lines as $id => $cartQuantity) {
			$purchasable = $this
				->get('elcodi.repository.purchasable')
				->find($id);

			if (!$purchasable instanceof PurchasableInterface) {
				throw new EntityNotFoundException('Purchasable not found');
			}

			$this
				->get('elcodi.manager.cart')
				->addPurchasable(
					$cart,
					$purchasable,
					intval($cartQuantity)
				);
		}

		return $this->redirect(
			$this->generateUrl('store_cart_view')
		);
	}

	/**
	 * Empty Cart
	 *
	 * @param CartInterface $cart Cart
	 *
	 * @return RedirectResponse
	 *
	 * @Route(
	 *      path = "/empty",
	 *      name="store_cart_empty",
	 *      methods = {"GET"}
	 * )
	 *
	 * @AnnotationEntity(
	 *      class = {
	 *          "factory" = "elcodi.wrapper.cart",
	 *          "method" = "get",
	 *          "static" = false,
	 *      },
	 *      name = "cart"
	 * )
	 */
	public function emptyCartAction(CartInterface $cart) {
		$this
			->get('elcodi.manager.cart')
			->emptyLines($cart);

		return $this->redirect(
			$this->generateUrl('store_homepage')
		);
	}

	/**
	 * Increment cartline
	 *
	 * @Route(
	 *      path = "/increment-cartline",
	 *      name = "increment_cartline"
	 * )
	 */
	public function incrementCartlineAction(Request $request) {
		$cartLineId = $request->get('cartLineId');
		$cart = $this
			->get('elcodi.wrapper.cart')
			->get();

		foreach ($cart->getCartLines() as $cartLine) {
			if ($cartLine->getId() == $cartLineId) {
				$this->get('elcodi.manager.cart')->increaseCartLineQuantity($cartLine, 1);
			}
		}

		$array = array(
			'status' => 'ok',
			'quantity' => $cartLine->getQuantity(),
		);
		return $response = new \Symfony\Component\HttpFoundation\JsonResponse($array);
	}

	/**
	 * decrement cartline
	 *
	 * @Route(
	 *      path = "/decrement-cartline",
	 *      name = "decrement_cartline"
	 * )
	 */
	public function decrementCartlineAction(Request $request) {
		$cartLineId = $request->get('cartLineId');
		$cart = $this
			->get('elcodi.wrapper.cart')
			->get();

		foreach ($cart->getCartLines() as $cartLine) {
			if ($cartLine->getId() == $cartLineId) {
				$this->get('elcodi.manager.cart')->decreaseCartLineQuantity($cartLine, 1);
			}
		}

		$array = array(
			'status' => 'ok',
			'quantity' => $cartLine->getQuantity(),
		);
		return $response = new \Symfony\Component\HttpFoundation\JsonResponse($array);
	}

	/**
	 * Update Cart
	 *
	 * @param FormInterface $form    Form
	 * @param CartInterface $cart    Cart
	 * @param boolean       $isValid Form is valid
	 *
	 * @return RedirectResponse
	 *
	 * @Route(
	 *      path = "/update",
	 *      name="store_cart_update",
	 *      methods = {"POST"}
	 * )
	 *
	 * @AnnotationEntity(
	 *      class = {
	 *          "factory" = "elcodi.wrapper.cart",
	 *          "method" = "get",
	 *          "static" = false,
	 *      },
	 *      name = "cart"
	 * )
	 * @AnnotationForm(
	 *      class = "store_cart_form_type_cart",
	 *      entity = "cart",
	 *      handleRequest = true,
	 *      validate = "isValid",
	 * )
	 */
	public function updateCartAction(
		FormInterface $form,
		CartInterface $cart,
		$isValid
	) {
		if ($isValid) {
			$this->get('elcodi.manager.cart')->removeEmptyLines($cart);
			$this
				->get('elcodi.object_manager.cart')
				->flush();
		}

		return $this->redirect(
			$this->generateUrl('store_cart_view')
		);
	}

	/**
	 * Deletes CartLine
	 *
	 * @param CartInterface     $cart     Cart
	 * @param CartLineInterface $cartLine Cart Line
	 *
	 * @return RedirectResponse
	 *
	 * @Route(
	 *      path = "/line/{id}/delete",
	 *      name="store_cartline_remove",
	 *      methods = {"GET"}
	 * )
	 *
	 * @AnnotationEntity(
	 *      class = {
	 *          "factory" = "elcodi.wrapper.cart",
	 *          "method" = "get",
	 *          "static" = false,
	 *      },
	 *      name = "cart"
	 * )
	 * @AnnotationEntity(
	 *      class = "elcodi.entity.cart_line.class",
	 *      name = "cartLine",
	 *      mapping = {
	 *          "id" = "~id~",
	 *      }
	 * )
	 */
	public function removeCartLineAction(
		CartInterface $cart,
		CartLineInterface $cartLine
	) {
		$this
			->get('elcodi.manager.cart')
			->removeLine(
				$cart,
				$cartLine
			);

		return $this->redirect(
			$this->generateUrl('store_cart_view')
		);
	}

	/**
	 * reduced version of cart
	 *
	 * @param CartInterface $cart Cart
	 *
	 * @return Response Response
	 *
	 * @Route(
	 *      path = "/nav",
	 *      name = "store_cart_nav",
	 *      methods = {"GET"}
	 * )
	 *
	 * @AnnotationEntity(
	 *      class = {
	 *          "factory" = "elcodi.wrapper.cart",
	 *          "method" = "get",
	 *          "static" = false,
	 *      },
	 *      name = "cart"
	 * )
	 */
	public function navAction(CartInterface $cart) {
		return $this->renderTemplate(
			'Subpages:cart-nav.html.twig',
			[
				'cart' => $cart,
			]
		);
	}

	/**
	 * Purchasable related view
	 *
	 * @param CartInterface $cart Cart
	 *
	 * @return array
	 *
	 * @Route(
	 *      path = "/related",
	 *      name = "store_cart_related",
	 *      methods = {"GET"}
	 * )
	 *
	 * @AnnotationEntity(
	 *      class = {
	 *          "factory" = "elcodi.wrapper.cart",
	 *          "method" = "get",
	 *          "static" = false,
	 *      },
	 *      name = "cart"
	 * )
	 */
	public function relatedAction(CartInterface $cart) {
		$purchasables = [];
		$cartLines = $cart->getCartLines();

		/**
		 * @var CartLineInterface $cartLine
		 */
		foreach ($cartLines as $cartLine) {
			$purchasables[] = $cartLine->getPurchasable();
		}

		$relatedPurchasables = $this
			->get('elcodi.related_purchasables_provider')
			->getRelatedPurchasablesFromArray($purchasables, 3);

		return $this->renderTemplate('Modules:_purchasable-related.html.twig', [
			'purchasables' => $relatedPurchasables,
		]);
	}
}
