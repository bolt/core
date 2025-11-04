<?php

namespace Bolt\Event\Subscriber;

use Bolt\Entity\User;
use Bolt\Log\LoggerTrait;
use Bolt\Repository\UserAuthTokenRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use UAParser\Parser;

class AuthSubscriber implements EventSubscriberInterface
{
    use LoggerTrait;

    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $em
    ) {
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        $request = $this->requestStack->getCurrentRequest();
        $user->setLastseenAt(new DateTime());
        $user->setLastIp($request->getClientIp());
        /** @var Parser $uaParser */
        $uaParser = Parser::create();
        $parsedUserAgent = $uaParser->parse($request->headers->get('User-Agent'))->toString();
        $sessionLifetime = $request->getSession()->getMetadataBag()->getLifetime();
        $expirationTime = (new DateTime())->modify('+' . $sessionLifetime . ' second');
        $userAuthToken = UserAuthTokenRepository::factory($user, $parsedUserAgent, $expirationTime);
        $user->setUserAuthToken($userAuthToken);

        $this->em->persist($user);
        $this->em->flush();

        $request->getSession()->set('user_auth_token_id', $userAuthToken->getId());
    }

    public function onLogout(LogoutEvent $event): void
    {
        if ($event->getToken() === null) {
            return;
        }

        /** @var User $user */
        $user = $event->getToken()->getUser();

        $request = $event->getRequest();
        $session = $request->getSession();

        $this->logger->notice('User \'{username}\' logged out (manually, auth_token: {token_id}, {ip})', [
            'id' => $user->getId(),
            'username' => $user->getUserIdentifier(),
            'token_id' => $authTokenId = $session->get('user_auth_token_id'),
            'ip' => $request->getClientIp(),
        ]);

        $session->invalidate();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationSuccessEvent::class => ['onAuthenticationSuccess'],
            LogoutEvent::class => ['onLogout'],
        ];
    }
}
