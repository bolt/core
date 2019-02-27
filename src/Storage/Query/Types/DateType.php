<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Ramsey\Uuid\Uuid;

class DateType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Date_'.Uuid::uuid4()->toString(),
            'type' => Type::string(),
        ];

        parent::__construct($config);
    }
}
