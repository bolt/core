<?php

declare(strict_types=1);

namespace Bolt\Content;

use Tightenco\Collect\Support\Collection;

final class ContentType extends Collection
{
    public function __call($name, $arguments)
    {
        return $this->get($name);
    }

    public static function factory(string $name, Collection $contentTypesConfig): ?self
    {
        if ($contentTypesConfig->has($name)) {
            return new self($contentTypesConfig->get($name));
        }

        return $contentTypesConfig
            ->filter(function (array $contentTypeConfig) use ($name): bool {
                return $contentTypeConfig['singular_slug'] === $name;
            })
            ->map(function (array $contentTypeConfig): self {
                return new self($contentTypeConfig);
            })
            ->first();
    }
}
