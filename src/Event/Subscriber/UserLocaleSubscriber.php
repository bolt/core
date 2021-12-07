<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Entity\User;
use Bolt\Event\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Stores the locale of the user in the session after the
 * login. This can be used by the LocaleSubscriber afterwards.
 */
class UserLocaleSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private $defaultLocale;

    /** @var Security */
    private $security;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack, string $defaultLocale, Security $security)
    {
        $this->defaultLocale = $defaultLocale;
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        $this->updateBackendLocale($user);
    }

    public function onUserEdit(UserEvent $event): void
    {
        // Update own locale on user edit
        if ($event->getUser() === $this->security->getUser()) {
            $this->updateBackendLocale($event->getUser());
        }
    }

    private function updateBackendLocale(User $user): void
    {
        $this->requestStack->getSession()->set('_backend_locale', $user->getLocale() ?? $this->defaultLocale);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
            UserEvent::ON_POST_SAVE => 'onUserEdit',
        ];
    }
}
