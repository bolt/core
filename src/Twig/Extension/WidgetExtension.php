<?php

namespace Bolt\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Widget functionality Twig extension.
 */
class WidgetExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $safe = ['is_safe' => ['html']];
        $env = ['needs_environment' => true];

        return [
            // @codingStandardsIgnoreStart
            new TwigFunction('countwidgets', [Runtime\WidgetRuntime::class, 'dummy'], $safe + $env),
            new TwigFunction('getwidgets',   [Runtime\WidgetRuntime::class, 'dummy'], $safe),
            new TwigFunction('haswidgets',   [Runtime\WidgetRuntime::class, 'dummy'], $safe + $env),
            new TwigFunction('widgets',      [Runtime\WidgetRuntime::class, 'dummy'], $safe + $env),
            // @codingStandardsIgnoreEnd
        ];
    }
}
