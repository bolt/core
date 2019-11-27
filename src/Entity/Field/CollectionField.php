<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CollectionField extends Field implements FieldInterface
{
    public function getType(): string
    {
        return 'collection';
    }

    private function getCollectionFieldValue(): string
    {
        return $this->value[0];
    }

    public function getValue(): array
    {
        $fieldDefinitions = $this->getDefinition()->get('fields');
        $result = [];

        $thisFieldValue = $this->getCollectionFieldValue();

        foreach ($fieldDefinitions as $fieldName => $fieldDefinition) {
            $databaseFieldName = $thisFieldValue . "::" . $fieldName;

            if ($this->getContent() && $this->getContent()->getField($this->getName())) {
                $setField = new SetField();
                $setField->setName((string) $databaseFieldName);
                $setField->setContent($this->getContent());
                $setField->setValue($thisFieldValue);
                $setField->setDefinition('fields', $fieldDefinition);

                $field = $setField;
            } else {
                $field = parent::factory($fieldDefinition, '', $fieldName);
                $field->setName($fieldName);
            }
            $result[$fieldName] = $field;
        }

        return $result;
    }
}
