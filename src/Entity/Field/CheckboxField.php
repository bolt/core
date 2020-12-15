<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CheckboxField extends Field implements FieldInterface, RawPersistable
{
    public const TYPE = 'checkbox';

    public function setValue($value): Field
    {
        switch ($value) {
            // String values come from the ContentEditController.
            case 'true':
                $value = true;
                break;
            case 'false':
                $value = false;
                break;
            default:
                $value = $value ? true : false;
        }

        return parent::setValue($value);
    }
}
