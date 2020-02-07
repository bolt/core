<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use ArrayIterator;
use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Bolt\Entity\FieldParentInterface;
use Bolt\Entity\FieldParentTrait;
use Bolt\Repository\FieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CollectionField extends Field implements FieldInterface, FieldParentInterface
{
    use FieldParentTrait;

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

    public function getValue(): array
    {
        if (! $this->getContent()) {
            return [];
        }

        $query = $this->getContent()->getRawFields()->filter(function (Field $field) {
            return $field->getParent() === $this;
        });

        /** @var ArrayIterator $iterator */
        $iterator = $query->getIterator();

        $iterator->uasort(function (Field $first, Field $second) {
            return (int) $first->getSortorder() > (int) $second->getSortorder() ? 1 : -1;
        });

        $fields = new ArrayCollection(iterator_to_array($iterator));

        $fields->map(function (Field $field): void {
            $field->setDefinition($field->getName(), $this->getDefinition()->get('fields')[$field->getName()]);
        });

        return $fields->toArray();
    }

    public function getDefaultValue()
    {
        $default = parent::getDefaultValue();

        if ($default === null) {
            return [];
        }

        $result = [];

        /** @var ContentType $type */
        foreach ($default as $key => $type) {
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
