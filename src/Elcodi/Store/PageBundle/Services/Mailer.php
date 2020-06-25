<?php

namespace Elcodi\Store\PageBundle\Services;

use Elcodi\Store\PageBundle\EventListener\Abstracts\AbstractEmailSenderEventListener;

class Mailer extends AbstractEmailSenderEventListener
{
    public function send($emailName, array $context, $receiverEmail, $bcc = false, $emailSender = null, $language = null)
    {
        return $this->sendEmail($emailName, $context, $receiverEmail, $bcc, $emailSender,$language);
    }
}
