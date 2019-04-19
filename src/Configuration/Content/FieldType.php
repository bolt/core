<?php

declare(strict_types=1);

namespace Bolt\Configuration\Content;

use Tightenco\Collect\Support\Collection;

class FieldType extends Collection
{
    public function __construct($items = [])
    {
        parent::__construct(
            static::defaults()->merge($items)
        );
    }

    private static function defaults(): Collection
    {
        return new Collection([
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
        ]);
    }

    public static function factory(string $name, ContentType $contentType): self
    {
        return new self($contentType->get('fields')->get($name, []));
    }

    public static function mock(string $name, Collection $definition): self
    {
        $definition['name'] = $name;

        return new self($definition);
    }
}
