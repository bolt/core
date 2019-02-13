<?php

declare(strict_types=1);

namespace Bolt\Collection;

use Tightenco\Collect\Support\Collection;

class DeepCollection extends Collection
{
    public static function deepMake($items): Collection
    {
        return parent::make($items)->map(function ($value) {
            if (is_array($value) || $value instanceof \Traversable) {
                return static::deepMake($value);
            }

            return $value;
        });
    }
}
