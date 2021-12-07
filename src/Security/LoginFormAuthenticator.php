<?php

declare(strict_types=1);

namespace Bolt\Security;

use Bolt\Entity\User;
use Bolt\Log\LoggerTrait;
use Bolt\Repository\UserAuthTokenRepository;
use Bolt\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use UAParser\Parser;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use LoggerTrait;

    /** @var UserRepository */
    private $userRepository;

    /** @var RouterInterface */
    private $router;

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var UserPasswordHasherInterface */
    private $passwordHasher;

    /** @var EntityManagerInterface */
    private $em;

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

    protected function getLoginUrl(): string
    {
        return $this->router->generate('bolt_login');
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'bolt_login' && $request->isMethod('POST');
    }

    public function getCredentials(Request $request): array
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

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        $token = new CsrfToken('login_csrf_token', $credentials['csrf_token']);

        if (! $this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        return $this->userRepository->findOneByCredentials($credentials['username']);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
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
            '_security.' . $providerKey . '.target_path',
            $fallback
        ));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        // Don't reveal a UsernameNotFound exception.
        if ($exception instanceof UsernameNotFoundException) {
            $exception = new BadCredentialsException();
        }

        parent::onAuthenticationFailure($request, $exception);

        // Redirect back to where we came from
        return new RedirectResponse($request->headers->get('referer'));
    }
}
