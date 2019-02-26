<?php

namespace Bolt\Storage\Query\Types;

use GraphQL\Type\Definition\ObjectType;

class RepeaterType extends ObjectType
{
    public function __construct(array $fields)
    {
        $config = [
            'name' => 'Repeater_'.md5(time().rand(1000,9999)),
            'fields' => $fields,
        ];
        parent::__construct($config);
    }
}