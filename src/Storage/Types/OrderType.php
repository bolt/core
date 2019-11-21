<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Types;

use GraphQL\Type\Definition\ObjectType;

class OrderType extends ObjectType
{
    public function __construct()
    {
        $config = [
        ];
        parent::__construct($config);
    }
}
