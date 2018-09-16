<?php

declare(strict_types=1);

namespace Bolt\Content;

use Bolt\Collection\Bag;

final class ContentTypeFactory
{
    public function __construct()
    {
    }

    /**
     * @param string $name
     * @param Bag    $contenttypesconfig
     *
     * @return ContentType
     */
    public static function get(string $name, Bag $contenttypesconfig)
    {
        $contentType = ContentType::from($contenttypesconfig[$name]);

        return $contentType;
    }
}
