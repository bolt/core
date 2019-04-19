<?php

declare(strict_types=1);

namespace Bolt\EventSubscriber;

use Bolt\Snippet\RequestZone;
use Bolt\Snippet\Target;
use Bolt\Widget\BoltHeaderWidget;
use Bolt\Widget\CanonicalLinkWidget;
use Bolt\Widget\NewsWidget;
use Bolt\Widget\SnippetWidget;
use Bolt\Widget\WeatherWidget;
use Bolt\Widgets;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class WidgetsSubscriber implements EventSubscriberInterface
{
    public const PRIORITY = 100;

    /** @var Widgets */
    private $widgets;

    public function __construct(Widgets $widgets)
    {
        $this->widgets = $widgets;
    }

    /**
     * Kernel request listener callback.
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        $this->widgets->registerWidget(new WeatherWidget());
        $this->widgets->registerWidget(new NewsWidget());
        $this->widgets->registerWidget(new CanonicalLinkWidget());
        $this->widgets->registerWidget(new BoltHeaderWidget());

        $metaTagSnippet = new SnippetWidget(
            '<meta name="generator" content="Bolt">',
            'Meta Generator tag snippet',
            Target::END_OF_HEAD,
            RequestZone::FRONTEND
        );

        $this->widgets->registerWidget($metaTagSnippet);
    }

    /**
     * Return the events to subscribe to.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', self::PRIORITY]],
        ];
    }
}
