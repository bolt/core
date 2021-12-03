<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Bolt\Entity\FieldParentInterface;
use Bolt\Entity\FieldParentTrait;
use Bolt\Entity\IterableFieldTrait;
use Bolt\Entity\ListFieldInterface;
use Bolt\Repository\FieldRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CollectionField extends Field implements FieldInterface, FieldParentInterface, ListFieldInterface, \Iterator, RawPersistable
{
    use FieldParentTrait;
    use IterableFieldTrait;

    public const TYPE = 'collection';

    public function getTemplates(): array
    {
        $fieldDefinitions = $this->getDefinition()->get('fields', []);
        $result = [];

        foreach ($fieldDefinitions as $fieldName => $fieldDefinition) {
            $templateField = FieldRepository::factory($fieldDefinition, '', $fieldName);
            $templateField->setDefinition($fieldName, $this->getDefinition()->get('fields')[$fieldName]);
            $templateField->setName($fieldName);
            $result[$fieldName] = $templateField;
        }

        return $result;
    }

    public function getApiValue()
    {
        $fields = $this->getValue();
        $result = [];

        foreach ($fields as $field) {
            $result[] = [
                'name' => $field->getName(),
                'type' => $field->getType(),
                'value' => $field->getApiValue(),
            ];
        }

        return $result;
    }

    /**
     * @param FieldInterface[] $fields
     */
    public function setValue($fields): Field
    {
        /** @var Field $field */
        foreach ($fields as $field) {
            // todo: This should be able to handle an array of fields
            // in key-value format, not just Field.php types.
            $field->setParent($this);
        }

        $this->fields = $fields;

        return $this;
    }

    public function getValue(): ?array
    {
        return $this->fields;
    }

    public function getDefaultValue()
    {
        $default = parent::getDefaultValue();

        if ($default === null) {
            return [];
        }

        $result = [];

        /** @var ContentType $type */
        foreach ($default as $type) {
            $value = $type->toArray()['default'];
            $name = $type->toArray()['field'];
            $definition = $this->getDefinition()->get('fields')[$name];
            $field = FieldRepository::factory($definition, $name);
            $field->setValue($value);
            $result[] = $field;
        }

        return $result;
    }
}
