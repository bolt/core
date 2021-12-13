<?php

namespace Bolt\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthenticationEntryPointRedirector implements AuthenticationEntryPointInterface
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // add a custom flash message and redirect to the login page
        $request->getSession()->getFlashBag()->add('warning', 'You have to login in order to access this page.');

        return new RedirectResponse($this->urlGenerator->generate('bolt_login'));
    }
}
