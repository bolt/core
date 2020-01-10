<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class SetField extends Field implements FieldInterface
{
    public function getType(): string
    {
        return 'set';
    }

    public function getValue(): array
    {
        $result = [];

        $fieldDefinitions = $this->getDefinition()->get('fields');

        // If there's no current $fieldDefinitions, we can return early
        if (! is_iterable($fieldDefinitions)) {
            return $result;
        }

        foreach ($fieldDefinitions as $name => $definition) {
            if ($this->getContent() && $this->hasChild($name)) {
                $field = $this->getChild($name);
                $field->setDefinition($name, $definition);
            } else {
                $field = parent::factory($definition);
            }

            $field->setName($name);
            $result[] = $field;
        }

        return $result;
    }

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
