<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Types;

use Exception;
use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
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
    /**
     * Serializes an internal value to include in a response.
     *
     * @throws Error
     */
    public function serialize($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format(\DateTime::W3C);
        }

        return null;
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * In the case of an invalid value this method must throw an Exception
     *
     * @throws Error
     */
    public function parseValue($value)
    {
        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input
     *
     * In the case of an invalid node or value this method must throw an Exception
     *
     * @param Node         $valueNode
     * @param mixed[]|null $variables
     *
     * @throws Exception
     */
    public function parseLiteral($valueNode, ?array $variables = null)
    {
        if ($valueNode instanceof StringValueNode) {
            return $valueNode->value;
        }

        throw new Exception();
    }
}
