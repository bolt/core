<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Bolt\Entity\FieldParentInterface;
use Bolt\Entity\FieldParentTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sirius\Upload\Util\Arr;

/**
 * @ORM\Entity
 */
class CollectionField extends Field implements FieldInterface, FieldParentInterface
{
    use FieldParentTrait;

    public function getType(): string
    {
        return 'collection';
    }

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
        if(! $this->getContent())
        {
            return [];
        }

        $query = $this->getContent()->getRawFields()->filter(function (Field $field) {
            return $field->getParent() === $this;
        });

        $iterator = $query->getIterator();

        $iterator->uasort(function (Field $first, Field $second){
            return (int) $first->getSortorder() > (int) $second->getSortorder() ? 1 : -1;
        });

        $fields = new ArrayCollection(iterator_to_array($iterator));

        $fields->map(function (Field $field){
            $field->setDefinition($field->getName(), $this->getDefinition()->get('fields')[$field->getName()]);
        });

        return $fields->toArray();

    }
}
