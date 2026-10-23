<?php

namespace Bolt\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthenticationEntryPointRedirector implements AuthenticationEntryPointInterface
{
    /**
     * @phpstan-ignore constructor.unusedParameter ($translator is kept for backwards compatibility, remove in 7.0. The flash message is translated when rendered)
     */
    public function __construct(
        TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function start(Request $request, ?AuthenticationException $authException = null): RedirectResponse
    {
        // add a custom flash message and redirect to the login page
        /** @var Session $session */
        $session = $request->getSession();
        $session->getFlashBag()->add('warning', 'You have to login in order to access this page.');

        return new RedirectResponse($this->urlGenerator->generate('bolt_login'));
    }
}
