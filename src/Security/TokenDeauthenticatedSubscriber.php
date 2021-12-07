<?php

namespace Bolt\Security;

use Bolt\Log\LoggerTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\TokenDeauthenticatedEvent;

class TokenDeauthenticatedSubscriber implements EventSubscriberInterface
{
    use LoggerTrait;

    public function onTokenDeauthenticated(TokenDeauthenticatedEvent $event): void
    {
        $this->logger->notice('User \'{username}\' had their token deauthenticated. (ip: {ip}; user agent: {user_agent})', [
            'username' => $event->getOriginalToken()->getUser()->getUserName(),
            'user_agent' => $event->getRequest()->getClientIp(),
        ]);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TokenDeauthenticatedEvent::class => 'onTokenDeauthenticated',
        ];
    }
}
