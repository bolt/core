<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class AuthenticationController.
 */
class AuthenticationController extends BaseController
{
    /**
     * @Route("/login", name="bolt_login")
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // last username entered by the user (if any)
        $last_username = $authenticationUtils->getLastUsername();

        // last authentication error (if any)
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->renderTemplate('security/login.html.twig', [
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
     * @Route("/logout", name="bolt_logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }

    /**
     * @Route("/resetpassword", name="bolt_resetpassword")
     */
    public function resetPassword(): Response
    {
        $twigVars = [
            'title' => 'Reset Password',
            'subtitle' => 'To reset your password, if you\'ve misplaced it',
        ];

        return $this->renderTemplate('pages/placeholder.html.twig', $twigVars);
    }
}
