<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Utils\Str;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class SlugField extends Field
{
    public function setValue(array $value): parent
    {
        $value = Str::slug(reset($value));
        $this->value = [$value];

        return $this;
    }

    public function getSlugPrefix()
    {
        $content = $this->getContent();

        if (! $content) {
            return '/foobar/';
        }

        return sprintf('/%s/', $content->getDefinition()->get('singular_slug'));
    }

    public function getSlugUseFields()
    {
        return (array) $this->getDefinition()->get('uses');
    }
}
