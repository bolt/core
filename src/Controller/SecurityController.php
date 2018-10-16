<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends BaseController
{
    /**
     * @Route("/bolt/login", name="bolt_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // last username entered by the user (if any)
        $last_username = $authenticationUtils->getLastUsername();

        // last authentication error (if any)
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->renderTemplate('security/login.twig', [
            'last_username' => $last_username,
            'error' => $error,
        ]);
    }

    /**
     * This is the route the user can use to logout.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the logout automatically. See logout in config/packages/security.yaml
     *
     * @Route("/bolt/logout", name="bolt_logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}
