<?php

declare(strict_types=1);

namespace Bolt\Twig\Runtime;

/**
 * Bolt specific Twig functions and filters that provide user functionality.
 */
class UserRuntime
{
    public function dummy($input = null)
    {
        return $input;
    }
}
