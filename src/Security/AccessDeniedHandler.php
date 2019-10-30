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

        $userRoles = $this->security->getUser()->getRoles();

        $this->logger->critical("CURRENT USER ROLES: " . implode (", ", $this->security->getUser()->getRoles()));

        if(in_array("ROLE_USER", $userRoles)){
            $this->logger->critical("THE CURRENT USER IS A ROLE_USER TYPE OF USER.");
            return;
        }


        #dump($accessDeniedException);
        #$this->logger->critical("LOGGER ACCESS DENIED EXCEPTION CRITICAL MESSAGE:" . $accessDeniedException->getMessage());
        #$this->logger->critical("CURRENT USER ROLES: " . implode (", ", $this->security->getUser()->getRoles()));

        $authenticationError = new DisabledUserLoginAttemptException();
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $authenticationError);
        $url = $this->router->generate('bolt_login');

        return new RedirectResponse($url);
    }
}