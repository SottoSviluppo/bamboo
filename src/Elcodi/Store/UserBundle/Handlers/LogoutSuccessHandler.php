<?php

namespace Elcodi\Store\UserBundle\Handlers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutSuccessHandler extends DefaultLogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    public function onLogoutSuccess(Request $request)
    {
        // $referer = $request->headers->get('referer');

        // $this->targetUrl = $referer;
        return $this->httpUtils->createRedirectResponse($request, $this->targetUrl);
    }
}
