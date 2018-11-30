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

    /**
     * @param Collection $contenttypesconfig
     *
     * @return ContentType
     */
    public static function factory(string $name, $contenttypesconfig): ?self
    {
        if ($contenttypesconfig[$name]) {
            return new self($contenttypesconfig[$name]);
        }

        foreach ($contenttypesconfig as $item => $value) {
            if ($value['singular_slug'] === $name) {
                return new self($contenttypesconfig[$item]);
            }
        }

        return null;
    }
}
