<?php

namespace Bolt\Storage\Query\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class DateType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Date_'.md5(time().rand(1000,9999)),
            'type' => Type::string(),
        ];

        parent::__construct($config);
    }
}