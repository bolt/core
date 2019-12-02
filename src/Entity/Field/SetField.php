<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class SetField extends Field implements FieldInterface, \JsonSerializable
{
    public function getType(): string
    {
        return 'set';
    }

    private function getHash(): string
    {
        return empty($this->value) ? uniqid() : $this->value[0];
    }

    public function getValue($jsonify = false): array
    {
        $hash = $this->getHash();
        $fieldDefinitions = $this->getDefinition()->get('fields');
        $result = [];
        $i = 0;

        foreach ($fieldDefinitions as $fieldName => $fieldDefinition) {
            $currentSetFieldName = $hash . '::' . $fieldName;

            if ($this->getContent() && $this->getContent()->hasField($currentSetFieldName)) {
                $field = $this->getContent()->getField($currentSetFieldName);
                $field->setLabel($fieldName);
            } else {
                $field = parent::factory($fieldDefinition, '', $fieldName);
                $field->setName($currentSetFieldName);
            }

            $result['fields'][$i] = $field;
            $i++;
        }

        $result['hash'] = $hash;

        return $result;
    }

    public function jsonSerialize()
    {
        $value = $this->getValue(true);

        return json_encode($value);
    }
}
