<?php

namespace Bolt\Storage\Query\Types;

use GraphQL\Type\Definition\ObjectType;

class ContentType extends ObjectType
{

    public function __construct(string $contentType, array $fields)
    {
        $config = [
            'name' => 'Content ' . $contentType,
            'fields' => function() use ($fields) {
                return $fields;
            },
        ];

        parent::__construct($config);
    }
}