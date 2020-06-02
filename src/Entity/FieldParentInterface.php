<?php

declare(strict_types=1);

namespace Bolt\Entity;

/**
 * Any Field entity that has child fields must implement this interface.
 */
interface FieldParentInterface
{
    public function setLocale(string $locale): Field;

    public function getValue(): ?array;
}
