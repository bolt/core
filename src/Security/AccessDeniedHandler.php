<?php

declare(strict_types=1);

namespace Bolt\Security;

use Bolt\Controller\TwigAwareController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Routing\RouterInterface;

class AccessDeniedHandler extends TwigAwareController implements AccessDeniedHandlerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $authenticationError = new DisabledUserLoginAttemptException();
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $authenticationError);
        $url = $this->router->generate('bolt_login');

        return new RedirectResponse($url);
    }
}