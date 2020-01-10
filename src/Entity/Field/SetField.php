<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\Common\Collections\ArrayCollection;
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
            $itemDbName = $this::getItemDbName($this->getName(), $name);

            if ($this->getContent() && $this->hasChild($itemDbName)) {
                $field = $this->getChild($itemDbName);
                $field->setDefinition($name, $definition);
            } else {
                $field = parent::factory($definition);
            }

            $field->setName($name);
            $result[] = $field;
        }

        return $result;
    }

    public static function getItemDbName($setName, $itemName)
    {
        return $itemName;
    }

    private function childrenFilter(): ArrayCollection
    {
        return $this->getContent()->getRawFields()->filter(function(Field $field)
        {
            return $field->getParent() === $this;
        });
    }

    public function getChild(string $fieldName): Field
    {
        return $this->getContent()->getRawFields()->filter(function(Field $field) use ($fieldName)
        {
            return $field->getParent() === $this && $field->getName() === $fieldName;
        })->first();
    }

    public function hasChild(string $fieldName): bool
    {
        $query = $this->getContent()->getRawFields()->filter(function(Field $field) use ($fieldName)
        {
            return $field->getParent() === $this && $field->getName() === $fieldName;
        });

        return ! $query->isEmpty();
    }
}
