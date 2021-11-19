<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Controller\Backend\Async\AsyncZoneInterface;
use Bolt\Controller\Backend\BackendZoneInterface;
use Bolt\Controller\ErrorZoneInterface;
use Bolt\Controller\Frontend\FrontendZoneInterface;
use Bolt\Widget\Injector\RequestZone;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ZoneSubscriber implements EventSubscriberInterface
{
    public const PRIORITY = 31;

    /**
     * Kernel request listener callback.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (RequestZone::getFromRequest($request) !== RequestZone::NOWHERE) {
            return;
        }

        $this->setZone($request);
    }

    /**
     * Sets the request's zone if needed and returns it.
     */
    public function setZone(Request $request): string
    {
        if (RequestZone::getFromRequest($request) !== RequestZone::NOWHERE) {
            return RequestZone::getFromRequest($request);
        }

        $zone = $this->determineZone($request);
        RequestZone::setToRequest($request, $zone);

        return $zone;
    }

    /**
     * Determine the zone and return it.
     */
    protected function determineZone(Request $request): string
    {
        if ($request->isXmlHttpRequest()) {
            return RequestZone::ASYNC;
        }

        $controller = $request->attributes->get('_controller');

        // If this happens, we're usually in the middle of handling an Exception
        if (! is_string($controller)) {
            return RequestZone::NOWHERE;
        }

        $controller = explode('::', $controller);

        try {
            $reflection = new ReflectionClass($controller[0]);

            if ($reflection->implementsInterface(BackendZoneInterface::class)) {
                return RequestZone::BACKEND;
            } elseif ($reflection->implementsInterface(FrontendZoneInterface::class)) {
                return RequestZone::FRONTEND;
            } elseif ($reflection->implementsInterface(AsyncZoneInterface::class)) {
                return RequestZone::ASYNC;
            } elseif ($reflection->implementsInterface(ErrorZoneInterface::class)) {
                return RequestZone::ERROR;
            }
        } catch (\ReflectionException $e) {
            // Alas…
        }

        return RequestZone::NOWHERE;
    }

    /**
     * Return the events to subscribe to.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            // Right after route is matched
            KernelEvents::REQUEST => [['onKernelRequest', self::PRIORITY]],
        ];
    }
}
