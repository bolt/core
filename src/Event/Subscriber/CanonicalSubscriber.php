<?php

namespace Bolt\Event\Subscriber;

use Bolt\Canonical;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CanonicalSubscriber implements EventSubscriberInterface
{
    private $canonical;

    public function __construct(Canonical $canonical)
    {
        $this->canonical = $canonical;
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
