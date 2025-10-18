<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Common\Json;
use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DataField extends Field implements FieldInterface
{
    public const TYPE = 'data';

    public function setValue($value): Field
    {
        if (Json::test($value)) {
            $value = Json::json_decode($value);
        }

        return parent::setValue($value);
    }
}
