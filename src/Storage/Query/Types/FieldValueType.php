<?php

namespace Bolt\Storage\Query\Types;

use GraphQL\Type\Definition\ObjectType;

class FieldValueType extends ObjectType
{
    public function __construct()
    {
        $config = [];
        parent::__construct($config);
    }
}