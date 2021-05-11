<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Twig\TokenParser\DumpTokenParser;
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
    /** @var string */
    protected $env;

    public function __construct(?string $env = null)
    {
        $this->env = $env ?? 'dev';
    }

    public function getFunctions(): array
    {
        // In DEV and TEST, we let Symfony\Bundle\DebugBundle handle this
        if (in_array($this->env, ['dev', 'test'], true)) {
            return [];
        }

        return [
            new TwigFunction('dump', [$this, 'dump']),
        ];
    }

    public function getTokenParsers(): array
    {
        // In DEV and TEST, we let Symfony\Bundle\DebugBundle handle this
        if (in_array($this->env, ['dev', 'test'], true)) {
            return [];
        }

        return [
            new DumpTokenParser(),
        ];
    }

    public function dump(): void
    {
    }
}
