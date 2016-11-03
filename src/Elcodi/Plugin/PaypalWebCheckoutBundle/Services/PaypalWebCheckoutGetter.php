<?php

namespace Elcodi\Plugin\PaypalWebCheckoutBundle\Services;

use Elcodi\Component\Payment\Entity\PaymentMethod;
use Elcodi\Component\Plugin\Entity\Plugin;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaypalWebCheckoutGetter
{
    protected $router;
    protected $plugin;

    public function __construct(
        UrlGeneratorInterface $router,
        Plugin $plugin
    ) {
        $this->router = $router;
        $this->plugin = $plugin;
    }

    public function getPaymentMethod()
    {
        if ($this
            ->plugin
            ->isUsable([
                'business',
            ])
        ) {
            $paypal = new PaymentMethod(
                $this
                    ->plugin
                    ->getHash(),
                'elcodi_plugin.paypal_web_checkout.name',
                'elcodi_plugin.paypal_web_checkout.description',
                $this
                    ->router
                    ->generate('paymentsuite_paypal_web_checkout_execute')
            );

            return $paypal;
        }
        return null;
    }
}
