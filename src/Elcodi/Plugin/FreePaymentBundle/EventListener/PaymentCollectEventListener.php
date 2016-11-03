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

namespace Elcodi\Plugin\FreePaymentBundle\EventListener;

use Elcodi\Component\Payment\Event\PaymentCollectionEvent;
use Elcodi\Component\Plugin\Entity\Plugin;

class PaymentCollectEventListener
{
    protected $paymentGetter;

    public function __construct(
        $paymentGetter
    ) {
        $this->paymentGetter = $paymentGetter;
    }

    /**
     * Add Free payment method
     *
     * @param PaymentCollectionEvent $event Event
     */
    public function addFreePaymentPaymentMethod(PaymentCollectionEvent $event)
    {
        $paymentMethod = $this->paymentGetter->getPaymentMethod();
        if ($paymentMethod != null) {
            $event->addPaymentMethod($paymentMethod);
        }
    }
}
