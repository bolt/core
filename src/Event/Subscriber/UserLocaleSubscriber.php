<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Entity\User;
use Bolt\Event\UserEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Stores the locale of the user in the session after the
 * login. This can be used by the LocaleSubscriber afterwards.
 */
class UserLocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly string $defaultLocale,
        private readonly Security $security
    ) {
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();
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
            LoginSuccessEvent::class => 'onLoginSuccess',
            UserEvent::ON_POST_SAVE => 'onUserEdit',
        ];
    }
}
