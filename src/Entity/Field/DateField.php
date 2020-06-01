<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class DateField extends Field implements FieldInterface
{
    public const TYPE = 'date';

    public function getDefaultValue()
    {
        $default = parent::getDefaultValue();

        if ($default !== null) {
            // Flatpickr asks for milliseconds, strtotime returns unix timestmap in seconds
            return strtotime($default) * 1000;
        }

        return $default;
    }
}
