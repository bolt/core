<?php

declare(strict_types=1);

/**
 * @author Bob den Otter <bobdenotter@gmail.com>
 */

namespace Bolt\EventSubscriber;

use Bolt\Snippets\Zone;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ZoneSubscriber implements EventSubscriberInterface
{
    /**
     * Kernel request listener callback.
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();
        if (Zone::get($request)) {
            return;
        }

        $this->setZone($request);
    }

    /**
     * Sets the request's zone if needed and returns it.
     */
    public function setZone(Request $request): string
    {
        if ($zone = Zone::get($request)) {
            return $zone;
        }

        $zone = $this->determineZone($request);
        Zone::set($request, $zone);

        return $zone;
    }

    /**
     * Determine the zone and return it.
     */
    protected function determineZone(Request $request): string
    {
        if ($request->isXmlHttpRequest()) {
            return Zone::ASYNC;
        }

        $controller = $request->attributes->get('_controller');

        if (mb_strpos($controller, 'Bolt\Controller\Backend') === 0) {
            return Zone::BACKEND;
        } elseif (mb_strpos($controller, 'Bolt\Controller\Frontend') === 0) {
            return Zone::FRONTEND;
        }

        return Zone::NOWHERE;
    }

    /**
     * Return the events to subscribe to.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 31]], // Right after route is matched
        ];
    }
}
