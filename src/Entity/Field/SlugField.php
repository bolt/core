<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;
use Tightenco\Collect\Support\Collection;

/**
 * @ORM\Entity
 */
class SlugField extends Field implements FieldInterface
{
    public const TYPE = 'slug';

    public function setValue($value): parent
    {
        $this->value = $value;

        return $this;
    }

    public function getSlugUseFields(): array
    {
        return Collection::wrap($this->getDefinition()->get('uses'))->toArray();
    }
}
