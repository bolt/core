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

    public function getHash(): string
    {
        if (empty(parent::getValue())) {
            $this->setValue([uniqid()]);
        }

        return parent::getValue()[0];
    }

    public function getValue(): array
    {
        $result = [];

        $fieldDefinitions = $this->getDefinition()->get('fields');

        // If there's no current $fieldDefinitions, we can return early
        if (! is_iterable($fieldDefinitions)) {
            return $result;
        }

        foreach ($fieldDefinitions as $name => $definition) {
            $itemDbName = $this::getItemDbName($this->getName(), $name);

            if ($this->getContent() && $this->getContent()->hasField($itemDbName)) {
                $field = $this->getContent()->getField($itemDbName);
                $field->setDefinition($name, $definition);
            } else {
                $field = parent::factory($definition);
            }

            $field->setName($name);
            $result[] = $field;
        }

        return $result;
    }

    public static function getItemDbName($setName, $itemName)
    {
        return $setName . '::' . $itemName;
    }
}
