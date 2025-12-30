<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Extension\ExtensionController;
use Bolt\Extension\ExtensionRegistry;
use Bolt\Storage\Query;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExtensionSubscriber implements EventSubscriberInterface
{
    public const PRIORITY = 0;

    public function __construct(
        private readonly ContainerInterface $container,
        private readonly ExtensionRegistry $extensionRegistry,
        private readonly EntityManagerInterface $objectManager,
        private readonly Query $query
    ) {
    }

    /**
     * Kernel response listener callback.
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $this->extensionRegistry->initializeAll([
            'manager' => $this->objectManager,
            'container' => $this->container,
            'query' => $this->query,
        ]);

        $controller = $event->getController();
        if (is_array($controller)) {
            // method access
            $controller = $controller[0];
        }

        if ($controller instanceof ExtensionController) {
            $controller->injectObjects([
                'manager' => $this->objectManager,
                'query' => $this->query,
            ]);
        }
    }

    /**
     * Command response listener callback.
     */
    public function onConsoleCommand(): void
    {
        $this->extensionRegistry->initializeAll([
            'manager' => $this->objectManager,
            'container' => $this->container,
            'query' => $this->query,
        ], true);
    }

    /**
     * Return the events to subscribe to.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [['onKernelController', self::PRIORITY]],
            ConsoleEvents::COMMAND => [['onConsoleCommand', self::PRIORITY]],
        ];
    }
}
