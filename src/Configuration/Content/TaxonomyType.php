<?php

declare(strict_types=1);

namespace Bolt\Configuration\Content;

use Bolt\Collection\DeepCollection;
use Illuminate\Support\Collection;

class TaxonomyType extends DeepCollection
{
    public function __call($name, $arguments): mixed
    {
        return $this->get($name);
    }

    public static function factory(?string $name, Collection $taxonomyTypesConfig): self
    {
        if ($taxonomyTypesConfig->has($name)) {
            return new self($taxonomyTypesConfig->get($name));
        }

        $taxonomyType = $taxonomyTypesConfig
            ->filter(function (Collection $taxonomyTypeConfig) use ($name): bool {
                return $taxonomyTypeConfig['singular_slug'] === $name;
            })
            ->map(function (Collection $taxonomyTypeConfig): self {
                return new self($taxonomyTypeConfig);
            })
            ->first();

        if ($taxonomyType) {
            return $taxonomyType;
        }

        return new self([
            'name' => $name,
            'slug' => $name,
            'singular_slug' => $name,
            'singular_name' => $name,
            // when it is created on the fly
            'virtual' => true,
        ]);
    }

    public function getSlug(): string
    {
        return $this->get('slug');
    }
}
