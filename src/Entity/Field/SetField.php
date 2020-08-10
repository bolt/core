<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Bolt\Entity\FieldParentInterface;
use Bolt\Entity\FieldParentTrait;
use Bolt\Entity\ListFieldInterface;
use Bolt\Entity\ListFieldTrait;
use Bolt\Repository\FieldRepository;
use Doctrine\ORM\Mapping as ORM;
use Tightenco\Collect\Support\Collection;

/**
 * @ORM\Entity
 */
class SetField extends Field implements FieldInterface, FieldParentInterface, ListFieldInterface, \Iterator, RawPersistable
{
    use FieldParentTrait;
    use ListFieldTrait;

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

        $value = [];

        /** @var Field $field */
        foreach ($fields as $field) {
            $field->setParent($this);
            $value[$field->getName()] = $field;
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

        return array_merge($fieldsFromDefinition, $this->getDefaultValue(), $this->getValue());
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
