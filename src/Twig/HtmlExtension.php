<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Canonical;
use Bolt\Common\Str;
use Bolt\Utils\Html;
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
            new TwigFunction('redirect', [$this, 'redirect']),
            new TwigFunction('absolute_link', [$this, 'absoluteLink']),
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
            new TwigFilter('shy', [$this, 'shy'], $safe),
            new TwigFilter('placeholders', [$this, 'placeholders'], $safe),
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

    /**
     * Add 'soft hyphens' &shy; to a string, so that it won't break layout in HTML when
     * using strings without spaces or dashes.
     */
    public function shy(string $str): string
    {
        return Str::shyphenate($str);
    }

    /**
     * Simple redirect to given path
     */
    public function redirect(string $path): void
    {
        header("Location: {$path}");
        exit();
    }

    /**
     * Use relative_path to create a proper link to either a relative page, or
     * to an external source. In the below example, the editor can provide
     * either page/about, or https://boltcms.io, and both will work
     */
    public function absoluteLink(string $link): string
    {
        return Html::makeAbsoluteLink($link);
    }

    public function placeholders(?string $string = null, array $replacements = []): string
    {
        $baseReplacements = [
            'year' => date('Y'),
            'month' => date('m'),
            'day' => date('d'),
            'date' => date('Y-m-d'),
            'random' => bin2hex(random_bytes(4)),
        ];

        $replacements = array_merge($baseReplacements, $replacements);

        return Str::placeholders((string) $string, $replacements, true);
    }
}
