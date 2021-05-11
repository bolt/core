<?php

declare(strict_types=1);

namespace Bolt\Widget\Injector;

use Bolt\Widget\CacheAwareInterface;
use Bolt\Widget\RequestAwareInterface;
use Bolt\Widget\ResponseAwareInterface;
use Bolt\Widget\WidgetInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;
use Tightenco\Collect\Support\Collection;

class QueueProcessor
{
    /** @var HtmlInjector */
    protected $injector;

    /** @var array */
    private $matchedComments = [];

    /** @var int */
    private $matchedCommentsCount = 0;

    public function __construct(HtmlInjector $injector)
    {
        $this->injector = $injector;
    }

    public function guardResponse(Response $response, callable $process): Response
    {
        // First, gather all html <!-- comments -->, because they shouldn't be
        // considered for replacements. We use a callback, so we can fill our
        // $this->matchedComments array
        preg_replace_callback('/<!--(.*)-->/Uis', [$this, 'pregCallback'], $response->getContent());

        $process($response);

        // Finally, replace back ###comment### with its original comment.
        if (! empty($this->matchedComments)) {
            $html = preg_replace(array_keys($this->matchedComments), $this->matchedComments, $response->getContent(), 1);
            $response->setContent($html);
        }

        return $response;
    }

    public function process(Response $response, Request $request, Collection $queue, CacheInterface $cache, string $zone): void
    {
        /** @var WidgetInterface $widget */
        foreach ($queue as $widget) {
            if ($widget->getZone() === $zone || $widget->getZone() === RequestZone::EVERYWHERE) {
                if ($widget instanceof RequestAwareInterface) {
                    $widget->setRequest($request);
                }
                if ($widget instanceof ResponseAwareInterface) {
                    $widget->setResponse($response);
                }
                if ($widget instanceof CacheAwareInterface) {
                    $widget->setCache($cache);
                }
                $this->injector->inject($widget, $response);
            }
        }
    }

    /**
     * Callback method to identify comments and store them in the
     * matchedComments array.
     *
     * These will be put back after the replacements on the HTML are finished.
     */
    public function pregCallback(array $c): string
    {
        $key = '###bolt-comment-' . $this->matchedCommentsCount++ . '###';
        // Add it to the array of matched comments.
        $this->matchedComments['/' . $key . '/'] = $c[0];

        return $key;
    }
}
