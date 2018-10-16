<?php

declare(strict_types=1);

namespace Bolt\Twig\Runtime;

use Twig\Environment;

/**
 * Bolt specific Twig functions and filters for HTML.
 *
 * @internal
 */
class WidgetRuntime
{
    public function dummy(Environment $env, $input = null)
    {
        return $input;
    }
}
