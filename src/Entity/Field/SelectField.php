<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class SelectField extends Field
{
    public function getValue(): ?array
    {
        if (empty($this->value)) {
            $this->value = [];
        }
        return $this->value;
    }
}
