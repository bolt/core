<?php declare(strict_types=1);

namespace Bolt\Twig\Extension;

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
            // @codingStandardsIgnoreStart
            new TwigFunction('unique', [$this, 'dummy'], $safe),
            // @codingStandardsIgnoreEnd
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            // @codingStandardsIgnoreStart
            new TwigFilter('order',   [$this, 'dummy']),
            new TwigFilter('shuffle', [$this, 'dummy']),
            // @codingStandardsIgnoreEnd
        ];
    }
}
