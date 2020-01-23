<?php

declare(strict_types=1);

namespace Bolt\Entity;

/**
 * Any Field entity that has child fields must implement this interface.
 */
interface FieldParentInterface
{
    public function getChild(string $fieldName): Field;

    public function hasChild(string $fieldName): bool;

    public function hasChildren(): bool;

    public function getChildren(): array;
}
