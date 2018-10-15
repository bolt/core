<?php declare(strict_types=1);

namespace Bolt\Twig\Runtime;

/**
 * Bolt specific Twig functions and filters for HTML.
 */
class HtmlRuntime
{
    public function dummy($input = null)
    {
        return $input;
    }
}
