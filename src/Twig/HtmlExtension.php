<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Utils\Markdown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * HTML functionality Twig extension.
 */
class HtmlExtension extends AbstractExtension
{
    private $parser;

    public function __construct(Markdown $parser)
    {
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $safe = ['is_safe' => ['html']];

        return [
            new TwigFunction('markdown', [$this, 'markdown'], $safe),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        $safe = ['is_safe' => ['html']];

        return [
            new TwigFilter('markdown', [$this, 'markdown'], $safe),
        ];
    }

    /**
     * Transforms the given Markdown content into HTML content.
     */
    public function markdown(string $content): string
    {
        return $this->parser->toHtml($content);
    }
}
