<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Dump Twig extension.
 *
 * This is a (deliberately) empty extension. When the implementor switched a
 * site from DEV to PROD, it shouldn't break if there's a lingering `{{ dump }}`
 * left in the site. This Twig Extension acts as a fallback to prevent that.
 */
class DumpExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('dump', [$this, 'dump']),
        ];
    }

    public function dump(): void
    {
    }
}
