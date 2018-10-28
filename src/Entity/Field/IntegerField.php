<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class IntegerField extends Field
{
    public function __toString(): string
    {
        return (string) intval($this->value);
    }

    public function getValue(): ?array
    {
        return [ intval($this->value) ];
    }
}
