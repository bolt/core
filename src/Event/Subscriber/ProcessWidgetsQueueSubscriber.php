<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Widgets;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ProcessWidgetsQueueSubscriber implements EventSubscriberInterface
{
    public const PRIORITY = 0;

    public function __construct(
        private readonly Widgets $widgets
    ) {
    }

    /**
     * Kernel response listener callback.
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (! $event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();

        $this->widgets->processQueue($response);
    }

    /**
     * Return the events to subscribe to.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => [['onKernelResponse', self::PRIORITY]],
        ];
    }
}
