<?php

declare(strict_types=1);

namespace Bolt\Collection;

use Illuminate\Support\Collection;

class DeepCollection extends Collection
{
    public static function deepMake($items): self
    {
        if ($items instanceof self) {
            return $items;
        }

        return parent::make($items)->map(function ($value) {
            if (is_array($value) || $value instanceof \Traversable) {
                return static::deepMake($value);
            }

            return $value;
        });
    }

    public function isKeyEmpty($key): bool
    {
        return $this->has($key) && (
            ($this->get($key) instanceof Collection && $this->get($key)->isEmpty())
            || empty($this->get($key))
            );
    }

    public function isKeyNotEmpty($key): bool
    {
        return ! $this->isKeyEmpty($key);
    }
}
