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

        foreach ($contentTypesConfig as $item => $value) {
            if ($value['singular_slug'] === $name) {
                return new self($contentTypesConfig[$item]);
            }
        }

        return null;
    }
}
