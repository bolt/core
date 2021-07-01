<?php

declare(strict_types=1);

namespace Bolt\Enum;

interface EnumInterface
{
    public static function all(): array;

    public static function isValid(?string $status): bool;
}
