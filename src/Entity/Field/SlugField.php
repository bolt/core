<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Utils\Str;
use Doctrine\ORM\Mapping as ORM;
use Tightenco\Collect\Support\Collection;

/**
 * @ORM\Entity
 */
class SlugField extends Field
{
    public function setValue($value): parent
    {
        if (is_array($value)) {
            $value = reset($value);
        }
        $value = Str::slug($value);
        $this->value = [$value];

        return $this;
    }

    public function getSlugPrefix(): string
    {
        // @todo https://github.com/bolt/four/issues/188 allow empty slug prefix
        $content = $this->getContent();

        if (! $content) {
            //@todo remove this
            return '/foobar/';
        }

        return sprintf('/%s/', $content->getDefinition()->get('singular_slug'));
    }

    public function getSlugUseFields(): array
    {
        return Collection::wrap($this->getDefinition()->get('uses'))->toArray();
    }
}
