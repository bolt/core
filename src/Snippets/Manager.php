<?php

declare(strict_types=1);

namespace Bolt\Snippets;

use Tightenco\Collect\Support\Collection;

class Manager
{
    /** @var Collection */
    private $queue;

    public function __construct()
    {
        $this->queue = new Collection([]);
    }

    public function registerSnippet($name, $callback): void
    {
        dump('register Snippet ' . $name);
    }

    public function getSnippet($name): void
    {
    }

    public function getWidget($twig, $name)
    {
        $widget = $this->queue->where('name', $name)->first();

        if ($widget) {
            return $this->invoke($twig, $widget['callback']);
        }
    }

    public function getWidgets(): void
    {
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
    }
}
