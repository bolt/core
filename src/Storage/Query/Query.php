<?php

declare(strict_types=1);

namespace Bolt\Storage\Query;

use Bolt\Storage\Query\Parser\ContentFieldParser;
use Bolt\Storage\Query\Resolver\QueryFieldResolver;
use Bolt\Storage\Query\Scope\ScopeEnum;
use Bolt\Storage\Query\Types\QueryType;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class Query
{
    private $contentFieldParser;

    private $queryFieldResolver;

    public function __construct(ContentFieldParser $contentFieldParser, QueryFieldResolver $queryFieldResolver)
    {
        $this->contentFieldParser = $contentFieldParser;
        $this->queryFieldResolver = $queryFieldResolver;
    }

    public function getContent(string $textQuery): JsonResponse
    {
        $schema = new Schema([
            'query' => new QueryType(
                $this->contentFieldParser,
                $this->queryFieldResolver,
                ScopeEnum::DEFAULT
            ),
        ]);
        $result = GraphQL::executeQuery($schema, $textQuery);

        return new JsonResponse($result->toArray());
    }

    public function getContentForTwig(string $textQuery): JsonResponse
    {
        $schema = new Schema([
            'query' => new QueryType(
                $this->contentFieldParser,
                $this->queryFieldResolver,
                ScopeEnum::FRONT
            ),
        ]);
        $result = GraphQL::executeQuery($schema, $textQuery);

        return new JsonResponse($result->toArray());
    }
}
