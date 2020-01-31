<?php

declare(strict_types=1);

namespace Bolt\Configuration\Content;

use Tightenco\Collect\Support\Collection;

class FieldType extends Collection
{
    /**
     * @param array|Collection $items
     */
    public function __construct($items, ?string $slug = null)
    {
        if ($slug) {
            $items['slug'] = $slug;
        }

        parent::__construct(
            static::defaults()->merge($items)
        );
    }

    private static function defaults(): Collection
    {
        return new Collection([
            'slug' => '',
            'type' => '',
            'class' => '',
            'group' => '',
            'label' => '',
            'variant' => '',
            'mode' => '',
            'postfix' => '',
            'prefix' => '',
            'placeholder' => '',
            'sort' => '',
            'default' => '',
            'allow_twig' => false,
            'allow_html' => false,
            'info' => '',
            'sanitise' => false,
            'localize' => false,
            'separator' => false,
        ]);
    }

    public static function factory(string $name, ContentType $contentType): self
    {
        return new self($contentType->get('fields')->get($name, []));
    }

    public static function mock(string $name, ?Collection $definition = null): self
    {
        if (! $definition) {
            $definition = new Collection();
        }

        $definition['name'] = $name;

        return new self($definition);
    }
}
