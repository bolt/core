<?php

declare(strict_types=1);

namespace Bolt;

use Bolt\Widget\CacheAwareInterface;
use Bolt\Widget\Injector\QueueProcessor;
use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\RequestAwareInterface;
use Bolt\Widget\StopwatchAwareInterface;
use Bolt\Widget\TwigAwareInterface;
use Bolt\Widget\WidgetInterface;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Environment;

/**
 * @todo Determine if we should split this up into smaller classes
 */
class Widgets
{
    private readonly Collection $queue;
    private array $rendered = [];

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly QueueProcessor $queueProcessor,
        private readonly Environment $twig,
        private readonly CacheInterface $cache,
        private readonly Stopwatch $stopwatch
    ) {
        $this->queue = new Collection([]);
    }

    public function registerWidget(WidgetInterface $widget): void
    {
        if ($widget instanceof TwigAwareInterface) {
            $widget->setTwig($this->twig);
        }

        $this->queue->push($widget);
    }

    public function renderWidgetByName(string $name, array $params = []): string
    {
        $widget = $this->queue->filter(fn (WidgetInterface $widget): bool => $widget->getName() === $name)->first();

        if ($widget) {
            return (string) $this->invokeWidget($widget, $params);
        }

        return '';
    }

    public function renderWidgetsForTarget(string $target, array $params = []): string
    {
        $widgets = $this->filteredWidgets($target);

        $output = '';

        foreach ($widgets as $widget) {
            $output .= $this->invokeWidget($widget, $params);
        }

        return $output;
    }

    public function listWidgetsForTarget(string $target): Collection
    {
        return $this->filteredWidgets($target);
    }

    private function filteredWidgets(string $target): Collection
    {
        return $this->queue
            ->filter(fn (WidgetInterface $widget): bool => in_array($target, $widget->getTargets(), true))
            ->sortBy(fn (WidgetInterface $widget): int => $widget->getPriority());
    }

    private function invokeWidget(WidgetInterface $widget, array $params = []): ?string
    {
        if ($this->isRendered($widget)) {
            return $this->getRendered($widget);
        }

        if ($widget instanceof StopwatchAwareInterface) {
            $widget->startStopwatch($this->stopwatch);
        }

        if ($widget instanceof RequestAwareInterface) {
            $widget->setRequest($this->requestStack->getCurrentRequest());
        }

        if ($widget instanceof CacheAwareInterface) {
            $widget->setCache($this->cache);
        }

        // Call the magic `__invoke` method on the $widget object
        // It can return a string (even an empty one) or `null`
        $output = $widget($params);

        if ($widget instanceof StopwatchAwareInterface) {
            $widget->stopStopwatch();
        }

        // Set the output as rendered for this Request, unless invoking it
        // returned `null`
        if ($output !== null) {
            $this->setRendered($widget, $output);
        }

        return $output;
    }

    public function processQueue(Response $response): Response
    {
        // Don't try to modify the response body for streamed responses. Stuff will break, if we do.
        if ($response instanceof StreamedResponse) {
            return $response;
        }

        $request = $this->requestStack->getCurrentRequest();
        $zone = RequestZone::getFromRequest($request);
        if ($zone === RequestZone::NOWHERE) {
            return $response;
        }

        $queue = $this->queue;
        $cache = $this->cache;

        return $this->queueProcessor->guardResponse(
            $response,
            function (Response $response) use ($request, $queue, $cache, $zone): void {
                $this->queueProcessor->process($response, $request, $queue, $cache, $zone);
            }
        );
    }

    private function isRendered(WidgetInterface $widget): bool
    {
        return array_key_exists($widget->getName(), $this->rendered);
    }

    private function getRendered(WidgetInterface $widget): ?string
    {
        if (! $this->isRendered($widget)) {
            return null;
        }

        return $this->rendered[$widget->getName()];
    }

    private function setRendered(WidgetInterface $widget, ?string $output): void
    {
        $this->rendered[$widget->getName()] = $output;
    }
}
