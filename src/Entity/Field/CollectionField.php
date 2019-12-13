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
            if ($thisFieldValue['field_type'] === 'set') {
                $field = new SetField();
                $field->setContent($this->getContent());
                $field->setValue($thisFieldValue['field_reference']);
                $field->setDefinition($thisFieldValue['field_name'], $this->getDefinition()->get('fields')[$thisFieldValue['field_name']]);
                $field->setName($thisFieldValue['field_name']);
            } else {
                $fieldDBname = $this->getName() . '::' . $thisFieldValue['field_name'];
                $field = $this->getContent()->getField($fieldDBname);
//              The field value persists ALL the values for the same type collection items (e.g. all 'ages') in an array
//              To display the value for the current item, we set the value for the specific key only
//              As $this->getValue() is called multiple times, clone the object to ensure $field->setValue() is called once per instance
                $field = clone $field;
                $field->setName($thisFieldValue['field_name']);
                $field->setValue($field->getValue()[$thisFieldValue['field_reference']]);
                $field->setDefinition($thisFieldValue['field_name'], $this->getDefinition()->get('fields')[$thisFieldValue['field_name']]);
            }

            $result['fields'][$i] = $field;
            $i++;
        }

        foreach ($fieldDefinitions as $fieldName => $fieldDefinition) {
            $templateField = parent::factory($fieldDefinition, '', $fieldName);
            $templateField->setDefinition($fieldName, $this->getDefinition()->get('fields')[$fieldName]);
            $templateField->setName($fieldName);
            $result['templates'][$fieldName] = $templateField;
        }

        return $result;
    }
}
