<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Utils\Str;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Text functionality Twig extension.
 */
class TextExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('safestring', [$this, 'safeString']),
            new TwigFilter('slug', [$this, 'slug']),
            new TwigFilter('ucwords', [$this, 'ucwords']),
        ];
    }

    public function safeString($str, $strict = false, $extrachars = '')
    {
        return Str::makeSafe($str, $strict, $extrachars);
    }

    public function slug($str): string
    {
        return Str::slug((string) $str);
    }

    public function ucwords(string $string, string $delimiters = ''): string
    {
        if (! $string) {
            return '';
        }

        return ucwords($string, $delimiters);
    }
}
