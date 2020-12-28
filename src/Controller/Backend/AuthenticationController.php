<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend;

use Bolt\Controller\TwigAwareController;
use Bolt\Form\LoginType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends TwigAwareController implements BackendZoneInterface
{
    /**
     * @Route("/login", name="bolt_login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // Always redirect to dashboard if a users is still logged in
        if ($this->getUser()) {
            return $this->redirectToRoute('bolt_dashboard');
        }

        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('bolt_dashboard');
        }

        // last authentication error (if any)
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('@bolt/security/login.html.twig', [
            'error' => $error,
            'loginForm' => $form->createView(),
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
     *
     * @deprecated 4.2
     */
    public function resetPassword(): Response
    {
        @trigger_error(sprintf('The method "resetPassword" of the class "%s" is deprecated since 4.2 and will be removed in 5.0.', self::class), E_USER_DEPRECATED);

        return $this->redirectToRoute('bolt_forgot_password_request');
    }
}
