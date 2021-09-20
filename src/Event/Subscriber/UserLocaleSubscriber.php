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

    // public function onUserEdit(UserEvent $event): void
    // {
        
    // }

    //todo create this function
    public function onAdminUserEdit(UserEvent $event) : void
    {
        //Check if user role is admin || developer and if theyre editing a user
        if ($event->getUser()->getRoles() === 'ROLE_ADMIN' || 'ROLE_DEVELOPER') {
            //Set the new locale for the user but dont update session locale
            $this->updateUserBackendLocale($event->getUser());
        } else {
            //If not then do the standard update session
            $this->updateBackendLocale($event->getUser());
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
            UserEvent::ON_POST_SAVE => 'onAdminUserEdit',
        ];
    }
}
