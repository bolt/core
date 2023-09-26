<?php

declare(strict_types=1);

namespace Bolt\Configuration\Content;

use Bolt\Collection\DeepCollection;
use Tightenco\Collect\Support\Collection;

class ContentType extends DeepCollection
{
    public function __call($name, $arguments)
    {
        return $this->get($name);
    }

    public static function factory(?string $name, Collection $contentTypesConfig): self
    {
        if ($contentTypesConfig->has($name)) {
            return new self($contentTypesConfig->get($name));
        }

        $contentType = $contentTypesConfig
            ->filter(function (Collection $contentTypeConfig) use ($name): bool {
                return $contentTypeConfig['singular_slug'] === $name;
            })
            ->map(function (Collection $contentTypeConfig): self {
                return new self($contentTypeConfig);
            })
            ->first();

        if ($contentType) {
            return $contentType;
        }

        return new self([
            'name' => $name,
            'slug' => $name,
            'singular_slug' => $name,
            'singular_name' => $name,
            'locales' => new Collection(),
            'fields' => new Collection(),
            // when it is created on the fly
            'virtual' => true,
        ]);
    }

    public function getSlug(): string
    {
        return $this->get('slug');
    }
}
