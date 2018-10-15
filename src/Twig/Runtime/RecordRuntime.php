<?php declare(strict_types=1);

namespace Bolt\Twig\Runtime;

/**
 * Bolt specific Twig functions and filters that provide \Bolt\Legacy\Content manipulation.
 */
class RecordRuntime
{
    public function dummy($input = null)
    {
        return $input;
    }
}
