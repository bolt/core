<?php

declare(strict_types=1);

namespace Bolt;

use Bolt\Snippet\QueueProcessor;
use Bolt\Snippet\RequestZone;
use Bolt\Widget\RequestAware;
use Bolt\Widget\TwigAware;
use Bolt\Widget\WidgetInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Tightenco\Collect\Support\Collection;
use Twig\Environment;

/**
 * @todo Determine if we should split this up into smaller classes
 */
class Widgets
{
    /** @var Collection */
    private $queue;

    /** @var RequestStack */
    private $requestStack;

    /** @var QueueProcessor */
    private $queueProcessor;

    /** @var Environment */
    private $twig;

    public function __construct(RequestStack $requestStack, QueueProcessor $queueProcessor, Environment $twig)
    {
        $this->queue = new Collection([]);
        $this->requestStack = $requestStack;
        $this->queueProcessor = $queueProcessor;
        $this->twig = $twig;
    }

    public function registerWidget(WidgetInterface $widget): void
    {
        if ($widget instanceof TwigAware) {
            $widget->setTwig($this->twig);
        }

        $this->queue->push($widget);
    }

    public function renderWidgetByName(string $name): string
    {
        $widget = $this->queue->filter(function (WidgetInterface $widget) use ($name) {
            return $widget->getName() === $name;
        })->first();

        if ($widget) {
            if ($widget instanceof RequestAware) {
                $widget->setRequest($this->requestStack->getCurrentRequest());
            }

            return $widget();
        }
    }

    public function renderWidgetsForTarget(string $target): string
    {
        $widgets = $this->queue->filter(function (WidgetInterface $widget) use ($target) {
            return $widget->getTarget() === $target;
        })->sortBy(function (WidgetInterface $widget) {
            return $widget->getPriority();
        });

        $output = '';

        foreach ($widgets as $widget) {
            if ($widget instanceof RequestAware) {
                $widget->setRequest($this->requestStack->getCurrentRequest());
            }

            $output .= $widget();
        }

        return $output;
    }

    public function processQueue(Response $response): Response
    {
        $zone = RequestZone::getFromRequest($this->requestStack->getCurrentRequest());
        return $this->queueProcessor->process($response, $this->queue, $zone);
    }
}
