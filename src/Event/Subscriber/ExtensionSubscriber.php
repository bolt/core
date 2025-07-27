<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Extension\ExtensionRegistry;
use Bolt\Storage\Query;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExtensionSubscriber implements EventSubscriberInterface
{
    public const PRIORITY = 0;

    private array $objects = [];

    public function __construct(
        ContainerInterface $container,
        private readonly ExtensionRegistry $extensionRegistry,
        EntityManagerInterface $objectManager,
        Query $query
    ) {
        $this->objects = [
            'manager' => $objectManager,
            'container' => $container,
            'query' => $query,
        ];
    }

    /**
     * Kernel response listener callback.
     */
    public function onKernelResponse(ControllerEvent $event): void
    {
        $this->extensionRegistry->initializeAll($this->objects);
    }

    /**
     * Command response listener callback.
     */
    public function onConsoleResponse(ConsoleCommandEvent $event): void
    {
        $this->extensionRegistry->initializeAll($this->objects, true);
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
