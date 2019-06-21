<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class SelectField extends Field implements FieldInterface
{
    public static function getType(): string
    {
        return 'select';
    }

    public function getValue(): ?array
    {
        if (empty($this->value)) {
            $options = $this->getDefinition()->get('values');

            // Pick the first key from Collection, or the full value as string, like `entries/id,title`
            $this->value = [$options->keys()->first()];
        }

        return $this->value;
    }
}
