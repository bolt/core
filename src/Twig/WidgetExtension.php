<?php

declare(strict_types=1);

namespace Bolt\Twig\Extension;

use Bolt\Twig\Runtime;
use Twig\Environment;
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
            new TwigFunction('countwidgets', [$this, 'dummy'], $safe + $env),
            new TwigFunction('getwidgets', [$this, 'dummy'], $safe),
            new TwigFunction('haswidgets', [$this, 'dummy'], $safe + $env),
            new TwigFunction('widgets', [$this, 'dummy'], $safe + $env),
        ];
    }

    public function dummy(Environment $env, $input = null)
    {
        return $input;
    }
}
