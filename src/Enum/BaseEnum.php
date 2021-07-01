<?php

declare(strict_types=1);

namespace Bolt\Enum;

use Tightenco\Collect\Support\Collection;

class BaseEnum implements EnumInterface
{
    /**
     * @return string[]
     */
    public static function all(): array
    {
        $self = new \ReflectionClass(self::class);

        return $self->getConstants();
    }

    public static function isValid(?string $status): bool
    {
        if ($status === null) {
            return false;
        }

        return (new Collection(static::all()))->containsStrict($status);
    }
}
