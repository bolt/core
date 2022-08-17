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

    protected static function defaults(): Collection
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
            'height' => '10',
            'icon' => '',
            'maxlength' => '',
            'autocomplete' => true,
            'values' => [],
            'min' => 0,
            'max' => 100000,
            'extra' => [],
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
                // This was here before due to extensions adding "services" as parent.
                // But also, it prevents breakage when there's inconsistencies between
                // the database and the definition. ¯\_(ツ)_/¯
                if (! $definition->get('fields')) {
                    continue;
                }

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
