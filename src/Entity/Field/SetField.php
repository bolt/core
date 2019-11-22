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
        $data = parent::getValue() ?: [];

        $result = [];

        foreach($data as $key => $field){
            $collection = collect($field);
            $generatedField = parent::factory($collection);
            $generatedField->setName((string) $key);
            //$field['value']['type'] = $generatedField->getType();
            $generatedField->setValue($field['value']);

            $output = [];
            $output['type'] = $generatedField->getType();
            $output['value'] = $generatedField->getValue();
            array_push($result, $output);
        }

        dump($result);
        return $result;
    }
}
