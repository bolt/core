<?php

declare(strict_types=1);

namespace Bolt\Twig\Extension;

use Bolt\Twig\Runtime;
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
            new TwigFunction('countwidgets', [Runtime\WidgetRuntime::class, 'dummy'], $safe + $env),
            new TwigFunction('getwidgets', [Runtime\WidgetRuntime::class, 'dummy'], $safe),
            new TwigFunction('haswidgets', [Runtime\WidgetRuntime::class, 'dummy'], $safe + $env),
            new TwigFunction('widgets', [Runtime\WidgetRuntime::class, 'dummy'], $safe + $env),
        ];
    }
}
