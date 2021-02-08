<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Bolt\Entity\IterableFieldTrait;
use Doctrine\ORM\Mapping as ORM;
use Tightenco\Collect\Support\Collection;

/**
 * @ORM\Entity
 */
class SelectField extends Field implements FieldInterface, RawPersistable, \Iterator
{
    use IterableFieldTrait;

    public const TYPE = 'select';

    public function setValue($value): Field
    {
        try {
            if (is_string($value)) {
                $value = json_decode($value, false);
            }
        } finally {
            // Array_filter filters out empty elements, but has the side effect
            // of making the array associative. We use Array_values to ensure we
            // have a sequential array.
            parent::setValue(array_values(array_filter((array) $value)));
        }

        return $this;
    }

    public function getValue(): ?array
    {
        $value = parent::getValue();

        if (empty($value) && $this->getDefinition()->get('required')) {
            $value = $this->getDefinition()->get('values');

            // Pick the first key from Collection, or the full value as string, like `entries/id,title`
            if ($value instanceof Collection) {
                $value = $value->keys()->first();
            }
        }

        return array_filter((array) $value);
    }

    public function getOptions()
    {
        return $this->getDefinition()->get('values');
    }

    public function getSelected()
    {
        // "ContentSelect" select, with ids of other content
        if ($this->isContentSelect()) {
            return new Collection(parent::getValue());
        }

        // "Normal" select, with options
        return $this->getOptions()->intersectByKeys(array_flip(parent::getValue()));
    }

    public function getSelectedIds()
    {
        return implode(' || ', parent::getValue());
    }

    public function getContentType()
    {
        $values = $this->getDefinition()->get('values');

        if (is_string($values) && mb_strpos($values, '/') !== false) {
            return current(explode('/', $values));
        }

        return false;
    }

    public function isContentSelect(): bool
    {
        $values = $this->getDefinition()->get('values');

        if (is_string($values) && mb_strpos($values, '/') !== false) {
            return true;
        }

        return false;
    }

    public function getDefaultValue()
    {
        return [parent::getDefaultValue()];
    }
}
