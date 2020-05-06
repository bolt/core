<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Bolt\Entity\FieldParentInterface;
use Bolt\Entity\FieldParentTrait;
use Bolt\Repository\FieldRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class SetField extends Field implements FieldInterface, FieldParentInterface
{
    use FieldParentTrait;

    public const TYPE = 'set';

    public function getValue(): array
    {
        $value = parent::getValue();

        if (empty($value)) {
            // create new ones from the definition
            $fieldDefinitions = $this->getDefinition()->get('fields');

            if (! is_iterable($fieldDefinitions)) {
                return $value;
            }

            foreach ($fieldDefinitions as $name => $definition) {
                $field = FieldRepository::factory($definition);
                $field->setName($name);
                $value[$name] = $field;
            }
        }

        return $value;
    }

    public function setValue($fields): Field
    {
        if (! is_iterable($fields)) {
            return $this;
        }

        $definedFields = array_flip($this->getDefinition()->get('fields')->keys()->toArray());

        $value = [];

        /** @var Field $field */
        foreach ($fields as $field) {
            $field->setParent($this);
            $value[$field->getName()] = $field;
        }

        // Sorts the fields in the order specified in the definition
        $value = array_merge(array_intersect($definedFields, $value), $value);

        parent::setValue($value);

        return $this;
    }

    public function getApiValue()
    {
        $result = [];

        foreach ($this->getValue() as $key => $value) {
            $result[$key] = $value->getApiValue();
        }

        return $result;
    }
}
