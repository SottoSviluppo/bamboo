<?php

namespace Elcodi\Bridge\PaymentSuiteBridgeBundle\Services;

class OrderPaymentSetter
{
    private $container;
    private $paymentBridge;
    private $orderDirector;

    public function __construct(
        $container,
        $paymentBridge,
        $orderDirector
    ) {
        $this->container = $container;
        $this->paymentBridge = $paymentBridge;
        $this->orderDirector = $orderDirector;
    }

    public function setPaymentInOrder($getterName)
    {
        $paymentGetter = $this->container->get($getterName);
        $paymentMethod = $paymentGetter->getPaymentMethod();
        $order = $this->orderDirector->find($this->paymentBridge->getOrderId());
        $order->setPaymentMethod($paymentMethod);
        $this->orderDirector->save($order);
    }

}
