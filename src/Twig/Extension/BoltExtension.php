<?php declare(strict_types=1);

namespace Bolt\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Bolt base Twig functionality and definitions.
 */
class BoltExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $env = ['needs_environment' => true];

        return [
            // @codingStandardsIgnoreStart
            new TwigFunction('first', 'dummy', $env),
            new TwigFunction('last',  'dummy', $env),
            // @codingStandardsIgnoreEnd
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        $env = ['needs_environment' => true];
        $deprecated = ['deprecated' => true];

        return [
            new TwigFilter('ucfirst', 'dummy', $env + ['alternative' => 'capitalize']),
        ];
    }
}
