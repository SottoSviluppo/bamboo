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

namespace Elcodi\Bridge\PaymentSuiteBridgeBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Elcodi\Component\CartCoupon\Services\CartCouponManager;
use Elcodi\Component\Cart\Entity\Interfaces\OrderInterface;
use Elcodi\Component\Coupon\Factory\CustomerCouponFactory;
use Elcodi\Component\StateTransitionMachine\Machine\MachineManager;
use PaymentSuite\PaymentCoreBundle\Event\Abstracts\AbstractPaymentEvent;

/**
 * Class OrderToPaidEventListener
 */
class OrderToPaidEventListener
{
    /**
     * @var MachineManager
     *
     * MachineManager for payment
     */
    private $paymentMachineManager;

    /**
     * @var ObjectManager
     *
     * Order object manager
     */
    private $orderObjectManager;

    /**
     * @var ObjectManager
     *
     * StateLine object manager
     */
    private $stateLineObjectManager;

    /**
     * @var CartCouponManager
     *
     * CartCoupon manager
     */
    private $cartCouponManager;

    /**
     * @var CustomerCouponFactory
     *
     * CustomerCoupon factory
     */
    private $customerCouponFactory;

    /**
     * @var ObjectManager
     *
     * CustomerCoupon object manager
     */
    private $customerCouponObjectManager;

    /**
     * Construct method
     *
     * @param MachineManager $paymentMachineManager  Machine manager for payment
     * @param ObjectManager  $orderObjectManager     Order object manager
     * @param ObjectManager  $stateLineObjectManager StateLine object manager
     * @param CartCouponManager  $cartCouponManager CartCoupon object manager
     * @param CustomerCouponFactory  $customerCouponFactory CartCoupon object manager
     * @param ObjectManager  $customerCouponObjectManager CartCoupon object manager
     */
    public function __construct(
        MachineManager $paymentMachineManager,
        ObjectManager $orderObjectManager,
        ObjectManager $stateLineObjectManager,
        CartCouponManager $cartCouponManager,
        CustomerCouponFactory $customerCouponFactory,
        ObjectManager $customerCouponObjectManager
    ) {
        $this->paymentMachineManager = $paymentMachineManager;
        $this->orderObjectManager = $orderObjectManager;
        $this->stateLineObjectManager = $stateLineObjectManager;
        $this->cartCouponManager = $cartCouponManager;
        $this->customerCouponFactory = $customerCouponFactory;
        $this->customerCouponObjectManager = $customerCouponObjectManager;
    }

    /**
     * Completes the payment process when the payment.order.success event is raised.
     *
     * This means that we can change the order state to ACCEPTED
     *
     * @param AbstractPaymentEvent $event
     */
    public function setOrderToPaid(AbstractPaymentEvent $event)
    {
        $order = $event
            ->getPaymentBridge()
            ->getOrder();

        if (!$order instanceof OrderInterface) {
            throw new \LogicException(
                'Cannot retrieve Order from PaymentBridge'
            );
        }

        /**
         * We create the new entry in the payment state machine
         */
        $stateLineStack = $this
            ->paymentMachineManager
            ->transition(
                $order,
                $order->getPaymentStateLineStack(),
                'pay',
                'Order paid using ' . $event
                    ->getPaymentMethod()
                    ->getPaymentName()
            );

        $order->setPaymentStateLineStack($stateLineStack);

        /**
         * We save all the data
         */
        $this
            ->stateLineObjectManager
            ->persist($stateLineStack->getLastStateLine());

        $this
            ->stateLineObjectManager
            ->flush($stateLineStack->getLastStateLine());

        $this
            ->stateLineObjectManager
            ->flush($order);

        $cart = $order->getCart();
        $cartCoupons = $this->cartCouponManager->getCartCoupons($cart);
        foreach ($cartCoupons as $coupon) {
            $customerCoupon = $this->customerCouponFactory->create();
            $customerCoupon->setCustomer($cart->getCustomer());
            $customerCoupon->setCoupon($coupon->getCoupon());
            $customerCouponObjectManager = $this->customerCouponObjectManager;
            $customerCouponObjectManager->persist($customerCoupon);
            $customerCouponObjectManager->flush();
        }
    }
}
