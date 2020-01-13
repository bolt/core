<?php

declare(strict_types=1);

namespace Bolt\Entity;

/**
 * Implements the methods of the FieldParentInterface.
 */
trait FieldParentTrait
{
    abstract public function getContent(): ?Content;

    public function getChild(string $fieldName): Field
    {
        return $this->getContent()->getRawFields()->filter(function (Field $field) use ($fieldName) {
            return $field->getParent() === $this && $field->getName() === $fieldName;
        })->first();
    }

    public function hasChild(string $fieldName): bool
    {
        $query = $this->getContent()->getRawFields()->filter(function (Field $field) use ($fieldName) {
            return $field->getParent() === $this && $field->getName() === $fieldName;
        });

        return ! $query->isEmpty();
    }
}
