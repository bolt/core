<?php

declare(strict_types=1);

namespace Bolt\Twig\Extension;

use Bolt\Twig\Runtime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Bolt specific Twig functions and filters that provide array manipulation.
 *
 * @internal
 */
class ArrayExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $safe = ['is_safe' => ['html']];

        return [
            new TwigFunction('unique', [Runtime\ArrayRuntime::class, 'unique'], $safe),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('order', [Runtime\ArrayRuntime::class, 'order']),
            new TwigFilter('shuffle', [Runtime\ArrayRuntime::class, 'shuffle']),
        ];
    }
}
