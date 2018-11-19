<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Routing functionality Twig extension.
 */
class RoutingExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('canonical', [$this, 'dummy']),
            new TwigFunction('htmllang', [$this, 'dummy']),
        ];
    }

    public function dummy($input = null)
    {
        return $input;
    }
}
