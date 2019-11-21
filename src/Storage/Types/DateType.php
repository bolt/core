<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Types;

use DateTime;
use DateTimeInterface;
use Exception;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\LeafType;
use GraphQL\Type\Definition\ObjectType;
use Ramsey\Uuid\Uuid;

class DateType extends ObjectType implements LeafType
{
    public function __construct()
    {
        $config = [
            'name' => 'Date_'.Uuid::uuid4()->toString(),
            'type' => new \DateTime(),
        ];

        parent::__construct($config);
    }

    public function serialize($value)
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format(DateTime::W3C);
        }

        return null;
    }

    public function parseValue($value)
    {
        return $value;
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        if ($valueNode instanceof StringValueNode) {
            return $valueNode->value;
        }

        throw new Exception();
    }
}
