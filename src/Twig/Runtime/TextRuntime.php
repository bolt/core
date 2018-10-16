<?php

declare(strict_types=1);

namespace Bolt\Twig\Runtime;

/**
 * Bolt specific Twig functions and filters that provide text manipulation.
 */
class TextRuntime
{
    public function dummy($input = null)
    {
        return $input;
    }
}
