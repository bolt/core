<?php

namespace Bolt\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthenticationEntryPointRedirector implements AuthenticationEntryPointInterface
{
    private $translator;

    private $urlGenerator;

    public function __construct(TranslatorInterface $translator, UrlGeneratorInterface $urlGenerator)
    {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // add a custom flash message and redirect to the login page
        $request->getSession()->getFlashBag()->add('warning', $this->translator->trans('You have to login in order to access this page.', [], 'security'));

        return new RedirectResponse($this->urlGenerator->generate('bolt_login'));
    }
}
