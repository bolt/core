<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Common\Json;
use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class HiddenField extends Field implements FieldInterface, ScalarCastable, RawPersistable
{
    public const TYPE = 'hidden';

    public function setValue($value): Field
    {
        if (Json::test($value)) {
            $value = Json::json_decode($value);
        }

        return parent::setValue($value);
    }

    public function getValue(): ?array
    {
        return [$this->getDefinition()->get('default', null)];
    }
}
