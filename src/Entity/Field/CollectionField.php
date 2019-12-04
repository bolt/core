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

    private function getCollectionFieldValues(): array
    {
        return $this->value;
    }

    public function getValue(): array
    {
        $fieldDefinitions = $this->getDefinition()->get('fields');
        $result = [];

        $thisFieldValues = $this->getCollectionFieldValues();

        foreach ($thisFieldValues as $thisFieldValue) {
            foreach ($fieldDefinitions as $fieldName => $fieldDefinition) {
                $databaseFieldName = $thisFieldValue . '::' . $fieldName;

                if ($this->getContent() && $this->getContent()->hasField($this->getName())) {
                    $field = new SetField();
                    $field->setName((string) $databaseFieldName);
                    $field->setContent($this->getContent());
                    $field->setValue($thisFieldValue);
                    $field->setDefinition('fields', $fieldDefinition);
                    $field->setName($fieldName);
                } else {
                    $field = parent::factory($fieldDefinition, '', $fieldName);
                    $field->setName($fieldName);
                }

                $result['fields'][$databaseFieldName] = $field;
            }
        }

        foreach ($fieldDefinitions as $fieldName => $fieldDefinition){
            $templateField = parent::factory($fieldDefinition, '', $fieldName);
            $templateField->setName($fieldName);
            $result['templates'][$fieldName] = $templateField;
        }

        return $result;
    }
}
