<?php

namespace Elcodi\Store\UserBundle\Handlers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

class SuccessAuthenticationHandler extends DefaultAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        return parent::onAuthenticationSuccess($request, $token);
    }
}
