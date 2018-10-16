<?php

declare(strict_types=1);

namespace Bolt\Twig\Extension;

use Bolt\Twig\Runtime;
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
        $safe = ['is_safe' => ['html']];

        return [
            new TwigFilter('json_decode', [Runtime\TextRuntime::class, 'dummy']),
            new TwigFilter('safestring', [Runtime\TextRuntime::class, 'dummy'], $safe),
            new TwigFilter('slug', [Runtime\TextRuntime::class, 'dummy']),
        ];
    }
}
