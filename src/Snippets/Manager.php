<?php
/**
 *
 *
 * @author Bob den Otter <bobdenotter@gmail.com>
 */

namespace Bolt\Snippets;


use Tightenco\Collect\Support\Collection;

class Manager
{
    /** @var array */
    private $queue;

    public function __construct()
    {
        $this->queue= new Collection([]);
    }

    public function registerSnippet($name, $callback)
    {
        dump('register Snippet ' . $name);
    }

    public function getSnippet($name)
    {

    }

    public function registerWidget(BaseWidget $widget)
    {
        $this->queue->push([
            'priority' => $widget->getPriority(),
            'target' => $widget->getTarget(),
            'callback' => $widget,
        ]);
    }

    public function registerBoltSnippets()
    {
        $this->registerWidget(new WeatherWidget());
    }
    
}