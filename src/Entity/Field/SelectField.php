<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;
use Tightenco\Collect\Support\Collection;

/**
 * @ORM\Entity
 */
class SelectField extends Field implements FieldInterface
{
    public function getType(): string
    {
        return 'select';
    }

    public function setValue($value): Field
    {
        try {
            if (is_string($value)) {
                $value = json_decode($value, false);
            }
        } finally {
            $this->value = (array) $value;
        }

        return $this;
    }

    public function getValue(): ?array
    {
        if (empty($this->value)) {
            $this->value = $this->getDefinition()->get('values');

            // Pick the first key from Collection, or the full value as string, like `entries/id,title`
            if ($this->value instanceof Collection) {
                $this->value = $this->value->keys()->first();
            }
        }

        return (array) $this->value;
    }
}
