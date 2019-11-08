<?php

declare(strict_types=1);

namespace Bolt\Security;

use Bolt\Entity\User;
use Bolt\Entity\UserAuthToken;
use Bolt\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use UAParser\Parser;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    /** @var UserRepository */
    private $userRepository;

    /** @var RouterInterface */
    private $router;

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var ObjectManager */
    private $em;

    public function __construct(UserRepository $userRepository, RouterInterface $router, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder, ObjectManager $em)
    {
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('bolt_login');
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'bolt_login' && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        if (! $this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        return $this->userRepository->findOneByCredentials($credentials['username']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return empty($credentials['password']) ? false : $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $user = $token->getUser();

        if (! $user instanceof User) {
            return null;
        }

        if ($user->getUserAuthToken()) {
            $this->em->remove($user->getUserAuthToken());
            $this->em->flush();
        }

        $user->setLastseenAt(new \DateTime());
        $user->setLastIp($request->getClientIp());
        $parsedUserAgent = Parser::create()->parse($request->headers->get('User-Agent'))->toString();
        $sessionLifetime = $request->getSession()->getMetadataBag()->getLifetime();
        $expirationTime = (new \DateTime())->modify('+'.$sessionLifetime.' second');
        $userAuthToken = UserAuthToken::factory($user, $parsedUserAgent, $expirationTime);
        $user->setUserAuthToken($userAuthToken);

        $this->em->persist($user);
        $this->em->flush();

        return new RedirectResponse($request->getSession()->get(
            '_security.'.$providerKey.'.target_path',
            $this->router->generate('bolt_dashboard')
        ));
    }
}
