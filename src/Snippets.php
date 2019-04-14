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
use Bolt\Widget\WeatherWidget;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Tightenco\Collect\Support\Collection;
use Twig\Environment;

class Snippets
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

    /**
     * @param BaseWidget|string|callable $callback
     */
    public function registerSnippet(
        $callback,
        string $target = Target::NOWHERE,
        string $zone = Zone::FRONTEND,
        string $name = 'nameless snippet',
        int $priority = 100
    ): void {
        $this->queue->push([
            'priority' => $priority,
            'target' => $target,
            'name' => $name,
            'zone' => $zone,
            'callback' => $callback,
        ]);
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

    public function processQueue(Response $response): void
    {
        $zone = Zone::getFromRequest($this->request);
        $this->queueProcessor->process($response, $this->queue, $zone);
    }

    public function registerBoltSnippets(): void
    {
        $this->registerWidget(new WeatherWidget());
        $this->registerWidget(new NewsWidget());

        $this->registerSnippet('<meta name="generator" content="Bolt">', Target::END_OF_HEAD);
        $this->registerWidget(new CanonicalLinkWidget());
        $this->registerWidget(new BoltHeaderWidget());
    }
}
