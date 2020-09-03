<?php

declare(strict_types=1);

namespace Bolt\Utils;

class Recursion
{
    public static function detect(): bool
    {
        $backtrace = [];

        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $call) {
            if (array_key_exists('class', $call)) {
                $backtrace[] = $call['class'] . $call['type'] . $call['function'];
            } else {
                $backtrace[] = basename($call['file']) . '::' . $call['function'];
            }
        }

        // The one we called from is [1]
        $callee = $backtrace[1];

        $count = count(array_filter($backtrace, function ($a) use ($callee) {
            return $a === $callee;
        }));

        return $count > 1;
    }
}
