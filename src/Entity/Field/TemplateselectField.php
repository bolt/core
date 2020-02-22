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
class TemplateselectField extends Field implements FieldInterface
{
    public const TYPE = 'templateselect';

    public function __toString(): string
    {
        return $this->getTwigValue();
    }

    public function setValue($value): Field
    {
        if (Json::test($value)) {
            $value = Json::json_decode($value);
        }

        return parent::setValue($value);
    }
}
