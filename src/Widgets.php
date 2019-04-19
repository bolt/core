<?php

declare(strict_types=1);

namespace Bolt;

use Bolt\Snippet\QueueProcessor;
use Bolt\Snippet\Target;
use Bolt\Snippet\Zone;
use Bolt\Widget\BoltHeaderWidget;
use Bolt\Widget\CanonicalLinkWidget;
use Bolt\Widget\NewsWidget;
use Bolt\Widget\RequestAware;
use Bolt\Widget\SnippetWidget;
use Bolt\Widget\TwigAware;
use Bolt\Widget\WeatherWidget;
use Bolt\Widget\WidgetInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Tightenco\Collect\Support\Collection;
use Twig\Environment;

class Widgets
{
    /** @var Collection */
    private $queue;

    /** @var Request */
    private $request;

    /** @var QueueProcessor */
    private $queueProcessor;

    /** @var Environment */
    private $twig;

    public function __construct(RequestStack $requestStack, QueueProcessor $queueProcessor, Environment $twig)
    {
        $this->queue = new Collection([]);
        $this->request = $requestStack->getCurrentRequest();
        $this->queueProcessor = $queueProcessor;
        $this->twig = $twig;
    }

    public function registerWidget(WidgetInterface $widget): void
    {
        if ($widget instanceof RequestAware) {
            $widget->setRequest($this->request);
        }
        if ($widget instanceof TwigAware) {
            $widget->setTwig($this->twig);
        }

        $this->queue->push($widget);
    }

    public function renderWidgetByName(string $name): string
    {
        $widget = $this->queue->filter(function(WidgetInterface $widget) use ($name) {
            return $widget->getName() === $name;
        })->first();

        if ($widget) {
            return $widget();
        }
    }

    public function renderWidgetsForTarget(string $target): string
    {
        $widgets = $this->queue->filter(function(WidgetInterface $widget) use ($target) {
            return $widget->getTarget() === $target;
        })->sortBy(function (WidgetInterface $widget) {
            return $widget->getPriority();
        });

        $output = '';

        foreach ($widgets as $widget) {
            $output .= $widget();
        }

        return $output;
    }

    public function processQueue(Response $response): Response
    {
        $zone = Zone::getFromRequest($this->request);
        return $this->queueProcessor->process($response, $this->queue, $zone);
    }

    public function registerBoltWidgets(): void
    {
        $this->registerWidget(new WeatherWidget());
        $this->registerWidget(new NewsWidget());
        $this->registerWidget(new CanonicalLinkWidget());
        $this->registerWidget(new BoltHeaderWidget());

        $metaTagSnippet = new SnippetWidget(
            '<meta name="generator" content="Bolt">',
            'Meta Generator tag snippet',
            Target::END_OF_HEAD,
            Zone::FRONTEND
        );

        $this->registerWidget($metaTagSnippet);
    }
}
