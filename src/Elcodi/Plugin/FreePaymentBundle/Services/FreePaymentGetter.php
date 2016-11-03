<?php

namespace Elcodi\Plugin\FreePaymentBundle\Services;

use Elcodi\Component\Payment\Entity\PaymentMethod;
use Elcodi\Component\Plugin\Entity\Plugin;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FreePaymentGetter
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
            ->isUsable()
        ) {
            $freePayment = new PaymentMethod(
                $this
                    ->plugin
                    ->getHash(),
                'elcodi_plugin.free_payment.name',
                'elcodi_plugin.free_payment.description',
                $this
                    ->router
                    ->generate('paymentsuite_freepayment_execute')
            );

            return $freePayment;
        }
        return null;
    }
}
