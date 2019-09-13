<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Configuration\Config;
use Bolt\Extension\ExtensionRegistry;
use Bolt\Widgets;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

class ExtensionSubscriber implements EventSubscriberInterface
{
    public const PRIORITY = 0;

    /** @var ExtensionRegistry */
    private $extensionRegistry;

    /** @var Widgets */
    private $widgets;

    /** @var Config */
    private $config;

    /** @var Environment */
    private $twig;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(ExtensionRegistry $extensionRegistry, Widgets $widgets, Config $config, Environment $twig, EventDispatcherInterface $dispatcher)
    {
        $this->extensionRegistry = $extensionRegistry;
        $this->widgets = $widgets;
        $this->config = $config;
        $this->twig = $twig;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Kernel response listener callback.
     */
    public function onKernelResponse(ControllerEvent $event): void
    {
        $this->extensionRegistry->initializeAll($this->widgets, $this->config, $this->twig, $this->dispatcher);
    }

    /**
     * Command response listener callback.
     */
    public function onConsoleResponse(ConsoleCommandEvent $event): void
    {
        $this->extensionRegistry->initializeAll($this->widgets, $this->config, $this->twig, $this->dispatcher);
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
