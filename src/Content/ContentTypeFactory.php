<?php

declare(strict_types=1);

namespace Bolt\Content;

use Tightenco\Collect\Support\Collection;

final class ContentTypeFactory
{
    public function __construct()
    {
    }

    /**
     * @param string     $name
     * @param Collection $contenttypesconfig
     *
     * @return ContentType
     */
    public static function get(string $name, $contenttypesconfig): ?ContentType
    {
        if ($contenttypesconfig[$name]) {
            return new ContentType($contenttypesconfig[$name]);
        }

        foreach ($contenttypesconfig as $item => $value) {
            if ($value['singular_slug'] === $name) {
                return new ContentType($contenttypesconfig[$item]);
            }
        }

        return null;
    }
}
