<?php

declare(strict_types=1);

namespace Bolt\Enum;

use Tightenco\Collect\Support\Collection;

class UserStatus
{
    public const ENABLED = 'enabled';
    public const DISABLED = 'disabled';

    /**
     * @return string[]
     */
    public static function all(): array
    {
        $self = new \ReflectionClass(self::class);

        return array_values($self->getConstants());
    }

    public static function isValid(?string $status): bool
    {
        if ($status === null) {
            return false;
        }

        return (new Collection(static::all()))->containsStrict($status);
    }
}
