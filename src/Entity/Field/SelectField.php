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
    /** @var bool */
    protected $array = true;

    public function getValue(): ?array
    {
        if (empty($this->value)) {
            $this->value = [key($this->getDefinition()->get('values'))];
        }
        return $this->value;
    }
}
