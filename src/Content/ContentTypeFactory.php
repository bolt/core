<?php

declare(strict_types=1);

namespace Bolt\Content;

class ContentTypeFactory
{
    public function __construct()
    {
    }

    public static function get($name, $config)
    {
        $ct = ContentType::from($config[$name]);

        return $ct;
    }
}
