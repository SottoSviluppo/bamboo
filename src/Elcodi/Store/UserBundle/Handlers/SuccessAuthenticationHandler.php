<?php

namespace Elcodi\Store\UserBundle\Handlers;
 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;
 
class SuccessAuthenticationHandler extends DefaultAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
	public function onAuthenticationSuccess( Request $request, TokenInterface $token )
	{
		return parent::onAuthenticationSuccess($request, $token);
	} 
}