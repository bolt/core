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

    public function getValue(): array
    {
//        $key => this->getHash();
        $fieldDefinitions = $this->getDefinition()->get('fields');

        $result = [];

        foreach($fieldDefinitions as $fieldName => $fieldDefinition) {
            if($this->getContent()->hasField($fieldName)) {
                $field = $this->getContent()->getField($fieldName);
            }else{
                $field = parent::factory($fieldDefinition);
                $field->setName($fieldName);
            }
            $result[$fieldName] = $field;
        }

        dump($result);

        return $result;
    }

    private function getHash()
    {

    }
}
