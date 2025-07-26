<?php

namespace Bolt\Event\Subscriber;

use Bolt\Canonical;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CanonicalSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Canonical $canonical
    ) {
    }

    public function onKernelRequest(RequestEvent $event)
    {
        // ensure initialization with real request
        $this->canonical->getRequest();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 0],
            ],
        ];
    }
}
