<?php

declare(strict_types=1);

namespace Bolt\Content;

use Tightenco\Collect\Support\Collection;

final class FieldType extends Collection
{
    public function __construct($items = [])
    {
        parent::__construct(array_merge($this->defaults(), $items));
    }

    /**
     * @return array
     */
    private function defaults()
    {
        $values = [
            'type' => '',
            'class' => '',
            'group' => '',
            'label' => '',
            'variant' => '',
            'postfix' => '',
            'prefix' => '',
            'placeholder' => '',
            'sort' => '',
            'default' => '',
            'allowtwig' => false,
        ];

        return $values;
    }

    /**
     * @param string      $name
     * @param ContentType $contentType
     *
     * @return FieldType|null
     */
    public static function factory(string $name, ContentType $contentType): ?self
    {
        if (isset($contentType['fields'][$name])) {
            $field = new self($contentType['fields'][$name]);
        } else {
            $field = new self([]);
        }

        return $field;
    }

    /**
     * @param string $name
     * @param array  $definition
     *
     * @return FieldType|null
     */
    public static function mock(string $name, array $definition): ?self
    {
        $definition['name'] = $name;

        $field = new self($definition);

        return $field;
    }
}
