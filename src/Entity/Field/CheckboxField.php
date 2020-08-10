<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CheckboxField extends Field implements FieldInterface, ScalarCastable, RawPersistable
{
    public const TYPE = 'checkbox';

    public function getTwigValue()
    {
        return current($this->getValue());
    }
}
