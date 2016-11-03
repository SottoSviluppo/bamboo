<?php

namespace Elcodi\Plugin\BankwireBundle\Services;

use Elcodi\Component\Payment\Entity\PaymentMethod;
use Elcodi\Component\Plugin\Entity\Plugin;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BankwireGetter
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
            $bankwire = new PaymentMethod(
                $this
                    ->plugin
                    ->getHash(),
                'elcodi_plugin.bankwire.name',
                'elcodi_plugin.bankwire.description',
                $this
                    ->router
                    ->generate('paymentsuite_bankwire_execute')
            );

            return $bankwire;
        }
        return null;
    }
}
