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

    public function getValue(): array
    {
        $fieldDefinitions = $this->getDefinition()->get('fields');
        $result = [];

        foreach ($fieldDefinitions as $fieldName => $fieldDefinition) {
            $currentSetFieldName = $this->getName() . ':' . $fieldName;

            if ($this->getContent() && $this->getContent()->hasField($currentSetFieldName)) {
                $field = $this->getContent()->getField($currentSetFieldName);
                $field->setLabel($fieldName);
            } else {
                $field = parent::factory($fieldDefinition, '', $fieldName);
                $field->setName($currentSetFieldName);
            }
            $result[$currentSetFieldName] = $field;
        }

        dump($result);
        return $result;
    }
}
