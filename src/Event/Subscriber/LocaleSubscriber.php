<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private $defaultLocale;

    public function __construct(string $defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // try to see if the locale has been set as a _locale routing parameter
        if ($request->attributes->has('_locale')) {
            $locale = $request->attributes->get('_locale');
            $request->getSession()->set('_locale', $locale);
            $request->setLocale($locale);
        } elseif ($request->query->has('_locale')) {
            $locale = $request->query->get('_locale');
            $request->getSession()->set('_locale', $locale);
            $request->setLocale($locale);

            // Symfony does not take this query parameter into account in its own subscriber.
            // Symfony's listener has a lower priority and will thus be called later.
            // It will not check that the locale was set already and in case useAcceptLanguageHeader
            // (see https://symfony.com/doc/5.4/reference/configuration/framework.html#set-locale-from-accept-language) is set, it will
            // overwrite the locale that was just set.
            // @see https://github.com/symfony/symfony/blob/5.4/src/Symfony/Component/HttpKernel/EventListener/LocaleListener.php#L71
            $request->attributes->set('_locale', $locale);
        } elseif ($request->attributes->get('zone', false) === 'backend' && $request->getSession()->has('_backend_locale')) {
            $request->setLocale($request->getSession()->get('_backend_locale'));
        } else {
            $request->setLocale($this->defaultLocale);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
