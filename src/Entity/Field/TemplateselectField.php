<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class TemplateselectField extends Field implements FieldInterface
{
    public function getType(): string
    {
        return 'templateselect';
    }
}
