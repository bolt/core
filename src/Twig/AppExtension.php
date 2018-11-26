<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('order', [$this, 'dummy']),
            new TwigFilter('unique', [$this, 'unique']),
            new TwigFilter('localedatetime', [$this, 'dummy']),
            new TwigFilter('showimage', [$this, 'dummy']),
            new TwigFilter('excerpt', [$this, 'excerpt']),
            new TwigFilter('ucwords', [$this, 'ucwords']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('__', [$this, 'dummy'], ['is_safe' => ['html']]),
            new TwigFunction('image', [$this, 'dummy'], ['is_safe' => ['html']]),
            new TwigFunction('thumbnail', [$this, 'dummy'], ['is_safe' => ['html']]),
            new TwigFunction('widgets', [$this, 'dummy'], ['is_safe' => ['html']]),
            new TwigFunction('htmllang', [$this, 'dummy'], ['is_safe' => ['html']]),
            new TwigFunction('popup', [$this, 'dummy'], ['is_safe' => ['html']]),
        ];
    }

    public function dummy($input = null)
    {
        return $input;
    }

    public function ucwords($content, string $delimiters = ''): string
    {
        if (! $content) {
            return '';
        }

        return ucwords($content, $delimiters);
    }

    public function unique($array): array
    {
        return array_unique($array);
    }
}
