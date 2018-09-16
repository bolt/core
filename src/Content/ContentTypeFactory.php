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
     * @param Bag    $config
     *
     * @return ContentType
     */
    public static function get(string $name, Bag $config)
    {
        $ct = ContentType::from($config[$name]);

        return $ct;
    }
}
