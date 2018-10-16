<?php declare(strict_types=1);

namespace Bolt\Twig\Runtime;

/**
 * Bolt specific Twig functions and filters for HTML.
 *
 * @internal
 */
class WidgetRuntime
{
    public function dummy($input = null)
    {
        return $input;
    }
}
