<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Types;

use Bolt\Storage\Query\Parser\ContentFieldParser;
use Bolt\Storage\Query\Resolver\QueryFieldResolver;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Ramsey\Uuid\Uuid;

class QueryType extends ObjectType
{
    private $contentFieldParser;

    public function __construct(ContentFieldParser $contentFieldParser, QueryFieldResolver $queryResolver)
    {
        $this->contentFieldParser = $contentFieldParser;

        $config = [
            'name' => 'Query',
            'fields' => $this->generateQueryFields(),
            'resolveField' => function ($val, array $args, $context, ResolveInfo $info) use ($queryResolver) {
                return $queryResolver->resolve($args, $info);
            },
        ];

        parent::__construct($config);
    }

    private function generateQueryFields(): array
    {
        $contentFields = $this->contentFieldParser->getParsedContentFields();
        $queryContentFields = [];
        foreach ($contentFields as $contentType => $fields) {
            $queryContentFields[$contentType] = [
                'type' => Type::listOf(new ContentType($contentType, $fields)),
                'description' => 'Represents list of ' . $contentType,
                'args' => [
                    'first' => [
                        'type' => Type::id(),
                    ],
                    'last' => [
                        'type' => Type::id(),
                    ],
                    'limit' => [
                        'type' => Type::int(),
                        'defaultValue' => 10,
                    ],
                    'filter' => [
                        'type' => Type::getNullableType(new InputObjectType([
                            'name' => 'ContentFilterInput_' . Uuid::uuid4()->toString(),
                            'fields' => $this->contentFieldParser->getContentFilterFields($contentType),
                        ])),
                    ],
                ],
            ];
        }

        return array_merge($queryContentFields, [
            'hello' => Type::listOf(Type::string()),
        ]);
    }
}
