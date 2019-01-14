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
            $options = (array) $this->getDefinition()->get('values');

            // Pick the first key from array, or the full value as string, like `entries/id,title`
            $this->value = [key($options)];
        }

        return $this->value;
    }
}
