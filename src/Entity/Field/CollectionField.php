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

        $i = 0;
        foreach ($thisFieldValues as $thisFieldValue) {
            if($thisFieldValue['field_type'] == 'set') {
                $field = new SetField();
                $field->setContent($this->getContent());
                $field->setValue($thisFieldValue['field_reference']);
                $field->setDefinition($thisFieldValue['field_name'], $this->getDefinition()->get('fields')[$thisFieldValue['field_name']]);
                $field->setName($thisFieldValue['field_name']);
            } else {
                $field = $this->getContent()->getField($thisFieldValue['field_reference']);
                $field->setDefinition($thisFieldValue['field_name'], $this->getDefinition()->get('fields')[$thisFieldValue['field_name']]);
            }

            $result['fields'][$i] = $field;
            $i++;
        }

        foreach ($fieldDefinitions as $fieldName => $fieldDefinition) {
            $templateField = parent::factory($fieldDefinition, '', $fieldName);
            $templateField->setName($fieldName);
            $result['templates'][$fieldName] = $templateField;
        }

        return $result;
    }
}
