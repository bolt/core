<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class SlugField extends Field
{
    public function setValue(array $value): parent
    {
        $value = Slugify::create()->slugify(reset($value));
        $this->value = [$value];

        return $this;
    }
}
