<?php

declare(strict_types=1);

namespace Bolt\Snippet;

use Symfony\Component\HttpFoundation\Response;

/**
 * Snippet queue processor.
 *
 * @author Bob den Otter <bob@twokings.nl>
 */
class QueueProcessor
{
    /** @var Snippet[] Queue with snippets of HTML to insert. */
    protected $queue = [];

    /** @var Injector */
    protected $injector;

    /** @var array */
    private $matchedComments = [];

    /**
     * Constructor.
     */
    public function __construct(Injector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * Insert a snippet. And by 'insert' we actually mean 'add it to the queue,
     * to be processed later'.
     */
    public function add(SnippetAssetInterface $snippet): void
    {
        $this->queue[] = $snippet;
    }

    public function clear(): void
    {
        $this->queue = [];
    }

    public function process(Response $response, $queue, string $zone): void
    {
        // First, gather all html <!-- comments -->, because they shouldn't be
        // considered for replacements. We use a callback, so we can fill our
        // $this->matchedComments array
        preg_replace_callback('/<!--(.*)-->/Uis', [$this, 'pregCallback'], $response->getContent());

        /** @var Snippet $snippet */
        foreach ($this->queue as $snippet) {
            if ($snippet->getZone() === $zone) {
                $this->injector->inject($snippet, $response);
            }
            unset($this->queue[$key]);
        }

        // Finally, replace back ###comment### with its original comment.
        if (! empty($this->matchedComments)) {
            $html = preg_replace(array_keys($this->matchedComments), $this->matchedComments, $response->getContent(), 1);
            $response->setContent($html);
        }
    }

    /**
     * Get the queued snippets.
     *
     * @return \Bolt\Asset\Snippet\Snippet[]
     */
    public function getQueue(): array
    {
        return $this->queue;
    }

    /**
     * Callback method to identify comments and store them in the
     * matchedComments array.
     *
     * These will be put back after the replacements on the HTML are finished.
     *
     * @param string $c
     *
     * @return string The key under which the comment is stored
     */
    private function pregCallback($c): string
    {
        $key = '###bolt-comment-' . count((array) $this->matchedComments) . '###';
        // Add it to the array of matched comments.
        $this->matchedComments['/' . $key . '/'] = $c[0];

        return $key;
    }
}
