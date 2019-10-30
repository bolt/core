<?php

declare(strict_types=1);

namespace Bolt\Security;

use Bolt\Exception\DisabledUserLoginAttemptException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Routing\RouterInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private $router;
    private $logger;
    protected $security;

    public function __construct(RouterInterface $router, LoggerInterface $logger, Security $security)
    {
        $this->router = $router;
        $this->logger = $logger;
        $this->security = $security;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        if(in_array("ROLE_USER", $userRoles)){
            //TODO: This catches the case when a ROLE_USER tries to login to the backend. Currently throws AccessDenied.
            return;
        }else if(in_array("ROLE_EDITOR", $userRoles)  or in_array("ROLE_ADMIN", $userRoles)) {
            $authenticationError = new DisabledUserLoginAttemptException();
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $authenticationError);
            $url = $this->router->generate('bolt_login');

            return new RedirectResponse($url);
        }
    }
}