<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Canonical;
use Bolt\Configuration\Config;
use Bolt\Widget\BoltHeaderWidget;
use Bolt\Widget\CanonicalLinkWidget;
use Bolt\Widget\FlocOptOutHeader;
use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\Injector\Target;
use Bolt\Widget\MaintenanceModeWidget;
use Bolt\Widget\SnippetWidget;
use Bolt\Widgets;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class WidgetSubscriber implements EventSubscriberInterface
{
    public const PRIORITY = 100;

    public function __construct(
        private readonly Widgets $widgets,
        private readonly Canonical $canonical,
        private readonly Config $config,
        private readonly Environment $twig
    ) {
    }

    /**
     * Kernel request listener callback.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (! $event->isMainRequest()) {
            return;
        }

        if (! $this->config->get('general/omit_canonical_link')) {
            $canonicalLinkWidget = new CanonicalLinkWidget(
                $this->canonical,
                $this->config,
                $this->twig
            );

            $this->widgets->registerWidget($canonicalLinkWidget);
        }

        if (! $this->config->get('general/headers/allow_floc')) {
            $this->widgets->registerWidget(new FlocOptOutHeader());
        }

        if ($this->config->get('general/headers/powered_by')) {
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
