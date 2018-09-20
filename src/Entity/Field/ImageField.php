<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ImageField extends Field
{
    public function __toString(): string
    {
        $config = $this->getContent()->getConfig();

        $path = $config->path('files', false, $this->get('filename'));

        return $path;
    }
}
