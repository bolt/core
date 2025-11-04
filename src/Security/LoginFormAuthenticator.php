<?php

namespace Bolt\Security;

use Bolt\Entity\User;
use Bolt\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class LoginFormAuthenticator extends AbstractAuthenticator implements AuthenticatorInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly UserRepository $userRepository,
        private readonly RouterInterface $router
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'bolt_login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        /** @var array $login_form */
        $login_form = $request->request->get('login');

        $credentials = [
            'username' => $login_form['username'] ?? '',
            'password' => $login_form['password'] ?? '',
            'csrf_token' => $login_form['_token'] ?? '',
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        $badge = new UserBadge($credentials['username'], fn (string $identifier): ?User => $this->userRepository->findOneByCredentials($identifier));

        return new Passport($badge, new PasswordCredentials($credentials['password']), [
            new CsrfTokenBadge('login_csrf_token', $credentials['csrf_token']),
            new RememberMeBadge(),
        ]);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // @todo: Allow different roles to redirect to different pages on success.
        if ($request->get('_target_path', false)) {
            $fallback = $request->get('_target_path');
        } elseif ($this->security->isGranted('dashboard')) {
            $fallback = $this->router->generate('bolt_dashboard');
        } else {
            $fallback = $this->router->generate('homepage');
        }

        return new RedirectResponse($request->getSession()->get(
            '_security.' . $firewallName . '.target_path',
            $fallback
        ));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        // Redirect back to where we came from
        return new RedirectResponse($request->headers->get('referer'));
    }
}
