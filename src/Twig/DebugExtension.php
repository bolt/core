<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DebugExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('backtrace', $this->backtrace(...)),
        ];
    }

    public function backtrace(int $options = DEBUG_BACKTRACE_IGNORE_ARGS, int $limit = 25): array
    {
        return debug_backtrace($options, $limit);
    }
}
