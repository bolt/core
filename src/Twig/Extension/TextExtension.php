<?php declare(strict_types=1);

namespace Bolt\Twig\Extension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

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
        $safe = ['is_safe' => ['html']];

        return [
            // @codingStandardsIgnoreStart
            new TwigFilter('json_decode',    [Runtime\TextRuntime::class, 'jsonDecode']),
            new TwigFilter('safestring',     [Runtime\TextRuntime::class, 'safeString'], $safe),
            new TwigFilter('slug',           [Runtime\TextRuntime::class, 'slug']),
            // @codingStandardsIgnoreEnd
        ];
    }
}
