<?php

declare(strict_types=1);

namespace Bolt;

use Bolt\Snippet\QueueProcessor;
use Bolt\Snippet\Target;
use Bolt\Snippet\Zone;
use Bolt\Widget\BaseWidget;
use Bolt\Widget\BoltHeaderWidget;
use Bolt\Widget\CanonicalLinkWidget;
use Bolt\Widget\NewsWidget;
use Bolt\Widget\SnippetWidget;
use Bolt\Widget\WeatherWidget;
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

    public function registerWidget(BaseWidget $widget): void
    {
        $widget->setRequest($this->request);
        $widget->setTwig($this->twig);

        $this->queue->push([
            'priority' => $widget->getPriority(),
            'target' => $widget->getTarget(),
            'name' => $widget->getName(),
            'zone' => $widget->getZone(),
            'callback' => $widget,
        ]);
    }

    public function renderWidgetByName(string $name): string
    {
        $widget = $this->queue->where('name', $name)->first();

        if ($widget) {
            return $this($widget['callback']);
        }
    }

    public function renderWidgetsForTarget(string $target): string
    {
        $widgets = $this->queue->where('target', $target)->sortBy('priority');

        $output = '';

        foreach ($widgets as $widget) {
            $output .= $this($widget['callback']);
        }

        return $output;
    }

    /**
     * @param BaseWidget|string|callable $callback
     */
    public function __invoke($callback): string
    {
        if (is_string($callback)) {
            return $callback;
        } elseif ($callback instanceof BaseWidget) {
            return $callback();
        }

        return '<!-- No callback -->';
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

        $metaTagSnippet = (new SnippetWidget())
            ->setName('Meta Generator tag snippet')
            ->setTarget(Target::END_OF_HEAD)
            ->setZone(Zone::FRONTEND)
            ->setSnippet('<meta name="generator" content="Bolt">');

        $this->registerWidget($metaTagSnippet);
    }
}
