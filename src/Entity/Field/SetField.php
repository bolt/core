<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class SetField extends Field implements FieldInterface
{
    public function getType(): string
    {
        return 'set';
    }

    private function getHash(): string
    {
        if (empty($this->value)) {
            $this->value = [uniqid()];
        }

        return $this->value[0];
    }

    public function getValue(): array
    {
        $hash = $this->getHash();
        $fieldDefinitions = $this->getDefinition()->get('fields');
        $result = [];
        $i = 0;

        foreach ($fieldDefinitions as $fieldName => $fieldDefinition) {
            $currentSetFieldName = $hash . '::' . $fieldName;
            if ($this->getContent() && $this->getContent()->hasField($currentSetFieldName)) {
                $field = $this->getContent()->getField($currentSetFieldName);
            } else {
                $field = parent::factory($fieldDefinition, '', $fieldName);
            }

            $field->setName($fieldName);
            $result['fields'][$i] = $field;
            $i++;
        }

        $result['hash'] = $hash;

        return $result;
    }
}
