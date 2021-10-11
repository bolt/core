<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Entity\User;
use Bolt\Event\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Stores the locale of the user in the session after the
 * login. This can be used by the LocaleSubscriber afterwards.
 */
class UserLocaleSubscriber implements EventSubscriberInterface
{
    /** @var SessionInterface */
    private $session;

    /** @var string */
    private $defaultLocale;

    public function __construct(SessionInterface $session, string $defaultLocale)
    {
        $this->session = $session;
        $this->defaultLocale = $defaultLocale;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        $this->updateBackendLocale($user);
    }

    public function onUserEdit(UserEvent $event): void
    {
        //Update own locale on user edit
        if ($event->getUser()->getUsername() !== $this->session->get('Username')) {
            $this->updateBackendLocale($event->getUser());
        }
        //else update the set the backend locale for the current user
        else {
            $this->updateUserBackendLocale($event->getUser());
        }
    }

    public function updateUserBackendLocale(User $user): void
    {
        //Update user specific setting not the session
        $user->setLocale($user->getLocale() ?? $this->defaultLocale);
    }

    private function updateBackendLocale(User $user): void
    {
        $this->session->set('_backend_locale', $user->getLocale() ?? $this->defaultLocale);
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
            UserEvent::ON_POST_SAVE => 'onUserEdit',
        ];
    }
}
