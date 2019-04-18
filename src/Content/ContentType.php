<?php

declare(strict_types=1);

namespace Bolt\Content;

use Bolt\Collection\DeepCollection;
use Tightenco\Collect\Support\Collection;

class ContentType extends DeepCollection
{
    public function __call($name, $arguments)
    {
        return $this->get($name);
    }

    public static function factory(string $name, Collection $contentTypesConfig): self
    {
        if ($contentTypesConfig->has($name)) {
            return new self($contentTypesConfig->get($name));
        }

        return $contentTypesConfig
            ->filter(function (Collection $contentTypeConfig) use ($name): bool {
                return $contentTypeConfig['singular_slug'] === $name;
            })
            ->map(function (Collection $contentTypeConfig): self {
                return new self($contentTypeConfig);
            })
            ->first();
    }

    public function getSlug(): string
    {
        return $this->get('slug');
    }
}
