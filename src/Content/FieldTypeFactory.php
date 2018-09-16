<?php

declare(strict_types=1);

namespace Bolt\Content;

final class FieldTypeFactory
{
    public function __construct()
    {
    }

    /**
     * @param string      $name
     * @param ContentType $contentType
     *
     * @return FieldType
     */
    public static function get(string $name, ContentType $contentType)
    {
        $field = FieldType::from($contentType['fields'][$name]);

        return $field;
    }
}
