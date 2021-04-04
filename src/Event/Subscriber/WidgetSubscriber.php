<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Canonical;
use Bolt\Configuration\Config;
use Bolt\Widget\BoltHeaderWidget;
use Bolt\Widget\CanonicalLinkWidget;
use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\Injector\Target;
use Bolt\Widget\MaintenanceModeWidget;
use Bolt\Widget\SnippetWidget;
use Bolt\Widgets;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class WidgetSubscriber implements EventSubscriberInterface
{
    public const PRIORITY = 100;

    /** @var Widgets */
    private $widgets;

    /** @var Canonical */
    private $canonical;

    /** @var Config */
    private $config;

    public function __construct(Widgets $widgets, Canonical $canonical, Config $config)
    {
        $this->widgets = $widgets;
        $this->canonical = $canonical;
        $this->config = $config;
    }

    /**
     * Kernel request listener callback.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $this->widgets->registerWidget(new CanonicalLinkWidget($this->canonical));

        if (! $this->config->get('general/omit_powered_by_header')) {
            $this->widgets->registerWidget(new BoltHeaderWidget());
        }

        if (! $this->config->get('general/omit_meta_generator_tag')) {
            $metaTagSnippet = new SnippetWidget(
                '<meta name="generator" content="Bolt">',
                'Meta Generator tag snippet',
                Target::END_OF_HEAD,
                RequestZone::FRONTEND
            );

            $this->widgets->registerWidget($metaTagSnippet);
        }

        if ($this->config->get('general/maintenance_mode', 'false')) {
            $this->widgets->registerWidget(new MaintenanceModeWidget());
        }
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
