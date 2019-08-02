<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Extension\ExtensionRegistry;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class InitialiseExtensionsSubscriber implements EventSubscriberInterface
{
    public const PRIORITY = 0;

    /** @var ExtensionRegistry */
    private $extensionRegistry;

    public function __construct(ExtensionRegistry $extensionRegistry)
    {
        $this->extensionRegistry = $extensionRegistry;
    }

    /**
     * Kernel response listener callback.
     */
    public function onKernelResponse(ControllerEvent $event): void
    {
        $this->extensionRegistry->initializeAll();
    }

    /**
     * Command response listener callback.
     */
    public function onConsoleResponse(ConsoleCommandEvent $event): void
    {
        $this->extensionRegistry->initializeAll();
    }

    /**
     * Return the events to subscribe to.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [['onKernelResponse', self::PRIORITY]],
            ConsoleEvents::COMMAND => [['onConsoleResponse', self::PRIORITY]],
        ];
    }
}
