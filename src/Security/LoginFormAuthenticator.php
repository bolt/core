<?php

namespace Bolt\Security;

use Bolt\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class LoginFormAuthenticator extends AbstractAuthenticator implements AuthenticatorInterface
{
    /** @var UserRepository */
    private $userRepository;

    /** @var Security */
    private $security;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(
        UserRepository $userRepository,
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        Security $security,
        RequestStack $requestStack
    ) {
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordHasher = $passwordHasher;
        $this->em = $em;
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public function __construct(Security $security, UserRepository $userRepository, RouterInterface $router)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'bolt_login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request)
    {
        /** @var array $login_form */
        $login_form = $request->request->get('login');

        $credentials = [
            'username' => $login_form['username'],
            'password' => $login_form['password'],
            'csrf_token' => $login_form['_token'],
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        $badge = new UserBadge($credentials['username'], function(string $identifier) {
            return $this->userRepository->findOneByCredentials($identifier);
        });

        return new Passport($badge, new PasswordCredentials($credentials['password']), [
            new CsrfTokenBadge('login_csrf_token', $credentials['csrf_token'])
        ]);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /**
         * @var PasswordAuthenticatedUserInterface $user
         */
        return empty($credentials['password']) ? false : $this->passwordHasher->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?RedirectResponse
    {
        $user = $token->getUser();

        if (! $user instanceof User) {
            return null;
        }

        $user->setLastseenAt(new \DateTime());
        $user->setLastIp($request->getClientIp());
        /** @var Parser $uaParser */
        $uaParser = Parser::create();
        $parsedUserAgent = $uaParser->parse($request->headers->get('User-Agent'))->toString();
        $sessionLifetime = $request->getSession()->getMetadataBag()->getLifetime();
        $expirationTime = (new \DateTime())->modify('+' . $sessionLifetime . ' second');
        $userAuthToken = UserAuthTokenRepository::factory($user, $parsedUserAgent, $expirationTime);
        $user->setUserAuthToken($userAuthToken);

        $this->em->persist($user);
        $this->em->flush();

        $this->requestStack->getSession()->set('user_auth_token_id', $userAuthToken->getId());

        $userArr = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'token_id' => $userAuthToken->getId(),
            'user_agent' => $parsedUserAgent,
            'ip' => $request->getClientIp(),
        ];
        $this->logger->notice('User \'{username}\' logged in (manually, auth_token: {token_id}, {user_agent}, {ip})', $userArr);

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
