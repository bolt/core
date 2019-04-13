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

    public function __construct(RequestStack $requestStack, QueueProcessor $queueProcessor)
    {
        $this->queue = new Collection([]);
        $this->request = $requestStack->getCurrentRequest();
        $this->queueProcessor = $queueProcessor;
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

        $this->queue->push([
            'priority' => $widget->getPriority(),
            'target' => $widget->getTarget(),
            'name' => $widget->getName(),
            'zone' => $widget->getZone(),
            'callback' => $widget,
        ]);
    }

    public function getWidget(string $name, ?Environment $twig = null): string
    {
        $widget = $this->queue->where('name', $name)->first();

        if ($widget) {
            return $this->invoke($widget['callback'], $twig);
        }
    }

    public function getWidgets(string $target, Environment $twig): string
    {
        $widgets = $this->queue->where('target', $target)->sortBy('priority');

        $output = '';

        foreach ($widgets as $widget) {
            $output .= $this->invoke($widget['callback'], $twig);
        }

        return $output;
    }

    /**
     * @param BaseWidget|string|callable $callback
     */
    public function invoke($callback, ?Environment $twig = null): string
    {
        if (is_string($callback)) {
            return $callback;
        } elseif ($callback instanceof BaseWidget) {
            if ($twig !== null) {
                $callback->setTwig($twig);
            }
            return $callback();
        }

        return '<!-- No callback -->';
    }

    public function processQueue(Response $response): void
    {
        $zone = Zone::get($this->request);
        $this->queueProcessor->process($response, $this->queue, $zone);
    }

    public function registerBoltSnippets(): void
    {
        $this->registerWidget(new WeatherWidget());
        $this->registerWidget(new NewsWidget());

        $this->registerSnippet('<meta name="generator" content="Bolt">', Target::END_OF_HEAD);
        $this->registerWidget(new CanonicalLinkWidget());
        $this->registerWidget(new BoltHeaderWidget());

        // @todo Determine if a favicon is something we want in 2019, by default
        // $this->registerWidget(new FaviconWidget());
    }
}
