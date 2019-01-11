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

    private function defaults(): array
    {
        return [
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
    }

    public static function factory(string $name, ContentType $contentType): self
    {
        if (isset($contentType['fields'][$name])) {
            $field = new self($contentType['fields'][$name]);
        } else {
            $field = new self([]);
        }

        return $field;
    }

    public static function mock(string $name, array $definition): self
    {
        $definition['name'] = $name;

        return new self($definition);
    }
}
