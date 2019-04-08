<?php

declare(strict_types=1);

namespace Bolt\Snippet;

use Symfony\Component\HttpFoundation\Response;
use Tightenco\Collect\Support\Collection;

/**
 * Snippet queue processor.
 *
 * @author Bob den Otter <bob@twokings.nl>
 */
class QueueProcessor
{
    /** @var Injector */
    protected $injector;

    /** @var array */
    private $matchedComments = [];

    public function __construct(Injector $injector)
    {
        $this->injector = $injector;
    }

    public function process(Response $response, Collection $queue, string $zone): void
    {
        // First, gather all html <!-- comments -->, because they shouldn't be
        // considered for replacements. We use a callback, so we can fill our
        // $this->matchedComments array
        preg_replace_callback('/<!--(.*)-->/Uis', [$this, 'pregCallback'], $response->getContent());

        foreach ($queue as $snippet) {
            if ($snippet['zone'] === $zone) {
                $this->injector->inject($snippet, $response);
            }
            // unset($this->queue[$key]);
        }

        // Finally, replace back ###comment### with its original comment.
        if (! empty($this->matchedComments)) {
            $html = preg_replace(array_keys($this->matchedComments), $this->matchedComments, $response->getContent(), 1);
            $response->setContent($html);
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
        $key = '###bolt-comment-' . count((array) $this->matchedComments) . '###';
        // Add it to the array of matched comments.
        $this->matchedComments['/' . $key . '/'] = $c[0];

        return $key;
    }
}
