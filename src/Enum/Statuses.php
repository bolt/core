<?php

declare(strict_types=1);

namespace Bolt\Enum;

class Statuses
{
    public const PUBLISHED = 'published';
    public const HELD = 'held';
    public const TIMED = 'timed';
    public const DRAFT = 'draft';

    /**
     * @return string[]
     */
    public static function all(): array
    {
        return [
            static::PUBLISHED,
            static::HELD,
            static::TIMED,
            static::DRAFT,
        ];
    }

    public static function isValid(?string $status): bool
    {
        if ($status === null) {
            return false;
        }
        return collect(static::all())->containsStrict($status);
    }
}
