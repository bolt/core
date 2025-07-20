<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Bolt\Entity\IterableFieldTrait;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @ORM\Entity
 */
class SelectField extends Field implements FieldInterface, RawPersistable, \Iterator
{
    use IterableFieldTrait;

    public const TYPE = 'select';

    /** @var ContainerInterface|null */
    private static $container = null;

    public function setValue($value): Field
    {
        try {
            if (is_string($value)) {
                // Try to decode JSON, or wrap it as an array (used in conimex).
                $value = json_decode($value, false) ?? [$value];
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

        if (empty($value) && ! $this->allowEmpty()) {
            $value = $this->getDefinition()->get('values');

            // Pick the first key from Collection, or the full value as string, like `entries/id,title`
            if ($value instanceof Collection) {
                $value = $value->keys()->first();
            }
        }

        return array_filter((array) $value);
    }

    public function getParsedValue()
    {
        $parsedValue = parent::getParsedValue();

        if ($this->getDefinition()->get('multiple') && ! is_array($parsedValue)) {
            // Make sure that multiselects always return an array, even if there's only one item.
            $parsedValue = [$parsedValue];
        }

        return $parsedValue;
    }

    public function getOptions()
    {
        $values = $this->getDefinition()->get('values');

        // Check if it is a service
        if (self::$container && is_string($values) && self::$container->has($values)) {
            $class = self::$container->get($values);
            // the name of the function
            $func = 'getOptions';

            return $class->{$func}($this);
        }

        // Check if it is a callable
        if (is_callable($values)) {
            return call_user_func_array($values, [$this]);
        }

        // Assume it's an array of values
        return $values;
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
        $values = $this->getOptions();

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

    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }
}
