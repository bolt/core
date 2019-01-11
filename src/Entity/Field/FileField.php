<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class FileField extends Field
{
    /** @var bool */
    protected $isArray = true;
}
