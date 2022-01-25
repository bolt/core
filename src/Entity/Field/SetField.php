<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Bolt\Entity\FieldParentInterface;
use Bolt\Entity\FieldParentTrait;
use Bolt\Entity\IterableFieldTrait;
use Bolt\Entity\ListFieldInterface;
use Bolt\Repository\FieldRepository;
use Doctrine\ORM\Mapping as ORM;
use Tightenco\Collect\Support\Collection;

/**
 * @ORM\Entity
 */
class SetField extends Field implements FieldInterface, FieldParentInterface, ListFieldInterface, \Iterator, RawPersistable
{
    use FieldParentTrait;
    use IterableFieldTrait;

    public const TYPE = 'set';

    public function getValue(): ?array
    {
        return $this->fields;
    }

    public function setValue($fields): Field
    {
        if (! is_iterable($fields)) {
            return $this;
        }

        $definedFields = array_flip($this->getDefinition()->get('fields', new Collection())->keys()->toArray());

//        dump($definedFields);
        $value = [];

        foreach ($fields as $key => $field) {
            // todo: This should be able to handle an array of fields
            // in key-value format, not just Field.php types.

            dump($fields);

            // If the input is a Field instead of a value just parse it like normal
            if ($field instanceof Field) {
                $field->setParent($this);
                $value[$field->getName()] = $field;

                break;
            }

            // Create the Field var
            $newField = new Field();

            // Set the set as parent
            $newField->setParent($this);

            // Create the field with the key as name
            $newField->setDefinition($key, $this->getDefinition()->get('fields')[$key]);

            dump($newField);

            // Should set the value of the newly created field
            $newField->setValue($field);
            // Next step
            dump($newField);
//            $value[$newField->getName()] = $newField;
        }

        // Sorts the fields in the order specified in the definition
        $value = array_merge(array_flip(array_intersect(array_keys($definedFields), array_keys($value))), $value);

        $this->fields = $value;

        return $this;
    }

    public function setContent(?Content $content): Field
    {
        /** @var Field $child */
        foreach ($this->getValue() as $child) {
            if ($content !== null) {
                $content->addField($child);
            } else {
                $child->setContent($content);
            }
        }

        return parent::setContent($content);
    }

    public function getApiValue()
    {
        $result = [];

        foreach ($this->getValue() as $key => $value) {
            $result[$key] = $value->getApiValue();
        }

        return $result;
    }

    public function getValueForEditor(): array
    {
        $fieldsFromDefinition = $this->getFieldsFromDefinition();

        // Values from the database, but not of fields that are no longer in the definition.
        $value = array_intersect_key($this->getValue(), $fieldsFromDefinition);

        return array_merge($fieldsFromDefinition, $this->getDefaultValue(), $value);
    }

    private function getFieldsFromDefinition(): array
    {
        // create new fields from the definition
        $fieldDefinitions = $this->getDefinition()->get('fields');

        if (! is_iterable($fieldDefinitions)) {
            return [];
        }

        $fields = [];
        foreach ($fieldDefinitions as $name => $definition) {
            $fields[$name] = FieldRepository::factory($definition, $name);
        }

        return $fields;
    }

    public function getDefaultValue()
    {
        $defaultValues = parent::getDefaultValue() ?? [];
        $value = $this->getFieldsFromDefinition();

        foreach ($defaultValues as $name => $default) {
            if (array_key_exists($name, $value)) {
                $value[$name]->setValue($default);
            }
        }

        return $value;
    }
}
