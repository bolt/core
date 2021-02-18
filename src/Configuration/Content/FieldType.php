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
            'placeholder' => false,
            'sort' => '',
            'default' => null,
            'allow_twig' => false,
            'allow_html' => false,
            'info' => '',
            'sanitise' => false,
            'localize' => false,
            'separator' => false,
            'required' => false,
            'readonly' => false,
            'error' => false,
            'pattern' => false,
            'hidden' => false,
            'default_locale' => 'en',
            // 10 rows by default
            'height' => '10',
            'icon' => '',
            'maxlength' => '',
        ]);
    }

    public static function factory(string $name, ContentType $contentType, array $parents = []): self
    {
        $defaults = static::defaults();

        if (empty($parents)) {
            $fromContentType = new self($contentType->get('fields')->get($name, []));
        } else {
            $definition = $contentType;

            foreach ($parents as $parent) {
                $definition = $definition->get('fields')->get($parent, collect([]));
            }

            $fromContentType = $definition->get('fields', collect([]))->get($name, collect([]));
        }

        return new self($defaults->merge($fromContentType));
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
