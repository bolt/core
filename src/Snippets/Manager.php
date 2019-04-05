<?php

declare(strict_types=1);

namespace Bolt\Snippets;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tightenco\Collect\Support\Collection;

class Manager
{
    /** @var Collection */
    private $queue;

    /** @var Request */
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->queue = new Collection([]);
        $this->request = $requestStack->getCurrentRequest();
    }

    public function registerSnippet($name, $callback): void
    {
        dump('register Snippet ' . $name);
    }

    public function getSnippet($name): void
    {
    }

    public function getWidget($twig, $name): string
    {
        $widget = $this->queue->where('name', $name)->first();

        if ($widget) {
            return $this->invoke($twig, $widget['callback']);
        }
    }

    public function getWidgets($twig, string $target)
    {
        $widgets = $this->queue->where('target', $target)->sortBy('priority');

        $output = '';

        if ($widgets) {
            foreach ($widgets as $widget) {
                $output .= $this->invoke($twig, $widget['callback']);
            }
        }

        return $output;
    }

    public function invoke($twig, $callback)
    {
        if (is_string($callback)) {
            return $callback;
        } elseif ($callback instanceof BaseWidget) {
            $callback->setTwig($twig);
            return $callback->invoke();
        }

        return '<!-- No callback -->';
    }

    public function registerWidget(BaseWidget $widget): void
    {
        $widget->setRequest($this->request);

        $this->queue->push([
            'priority' => $widget->getPriority(),
            'target' => $widget->getTarget(),
            'name' => $widget->getName(),
            'callback' => $widget,
        ]);
    }

    public function registerBoltSnippets(): void
    {
        $this->registerWidget(new WeatherWidget());
        $this->registerWidget(new NewsWidget());
    }
}
