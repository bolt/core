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
        return collect($this->getValue())->filter(function (Field $field) use ($fieldName) {
            return $field->getName() === $fieldName;
        })->first();
    }

    public function hasChild(string $fieldName): bool
    {
        $query = collect($this->getValue())->filter(function (Field $field) use ($fieldName) {
            return $field->getName() === $fieldName;
        });

        return ! $query->isEmpty();
    }

    public function hasChildren(): bool
    {
        $query = collect($this->getValue())->filter(function (Field $field) {
            return $field->getParent() === $this;
        });

        return ! $query->isEmpty();
    }

    public function getChildren(): array
    {
        return $this->getValue();
    }

    public function setLocale(?string $locale): Field
    {
        parent::setLocale($locale);
        /** @var Field $child */
        foreach ($this->getChildren() as $child) {
            $child->setLocale($locale);
        }

        return $this;
    }
}
