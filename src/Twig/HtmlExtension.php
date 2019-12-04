<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Canonical;
use Bolt\Utils\Markdown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * HTML functionality Twig extension.
 */
class HtmlExtension extends AbstractExtension
{
    /** @var Markdown */
    private $markdown;

    /** @var Canonical */
    private $canonical;

    public function __construct(Markdown $markdown, Canonical $canonical)
    {
        $this->markdown = $markdown;
        $this->canonical = $canonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $safe = [
            'is_safe' => ['html'],
        ];

        return [
            new TwigFunction('canonical', [$this, 'canonical']),
            new TwigFunction('markdown', [$this, 'markdown'], $safe),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        $safe = [
            'is_safe' => ['html'],
        ];

        return [
            new TwigFilter('markdown', [$this, 'markdown'], $safe),
        ];
    }

    public function canonical(?string $route = null, array $params = [])
    {
        return $this->canonical->get($route, $params);
    }

    /**
     * Transforms the given Markdown content into HTML content.
     */
    public function markdown(string $content): string
    {
        return $this->markdown->parse($content);
    }
}
