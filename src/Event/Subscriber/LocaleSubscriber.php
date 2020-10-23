<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (! $request->hasPreviousSession()) {
            return;
        }
        // try to see if the locale has been set as a _locale routing parameter
        if ($request->attributes->has('_locale')) {
            $locale = $request->attributes->get('_locale');
            $request->getSession()->set('_locale', $locale);
            $request->setLocale($locale);
        } elseif ($request->query->has('_locale')) {
            $locale = $request->query->get('_locale');
            $request->getSession()->set('_locale', $locale);
            $request->setLocale($locale);
        } elseif ($request->attributes->get('zone', false) === 'backend' && $request->getSession()->has('_backend_locale')) {
            $request->setLocale($request->getSession()->get('_backend_locale'));
        } elseif ($request->getSession()->has('_locale')) {
            // @todo: This is probably never reached. Remove if you're brave enough ;-)
            // if no explicit locale has been set on this request, use one from the session
            $request->setLocale($request->getSession()->get('_locale'));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
