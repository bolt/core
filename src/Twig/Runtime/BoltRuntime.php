<?php declare(strict_types=1);

namespace Bolt\Twig\Runtime;

/**
 * Bolt extension runtime for Twig.
 */
class BoltRuntime
{
    public function dummy($input = null)
    {
        return $input;
    }
}
