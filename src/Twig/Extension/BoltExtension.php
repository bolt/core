<?php declare(strict_types=1);

namespace Bolt\Twig\Extension;

use Bolt\Twig\Runtime;
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
            new TwigFunction('first', [Runtime\BoltRuntime::class, 'dummy'], $env),
            new TwigFunction('last',  [Runtime\BoltRuntime::class, 'dummy'], $env),
            // @codingStandardsIgnoreEnd
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        $env = ['needs_environment' => true];

        return [
            new TwigFilter('ucfirst', [Runtime\BoltRuntime::class, 'dummy'], $env + ['alternative' => 'capitalize']),
        ];
    }
}
