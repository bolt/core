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

        $children = $this->getContent()->getFieldsByParent($this);
        return $children->toArray();
    }
}
