<?php

declare(strict_types=1);

namespace Bolt\Storage\Query;

use Bolt\Storage\Query\Parser\ContentFieldParser;
use Bolt\Storage\Query\Resolver\QueryFieldResolver;
use Bolt\Storage\Query\Types\QueryType;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;

class Query
{
    private $contentFieldParser;

    private $queryFieldResolver;

    public function __construct(ContentFieldParser $contentFieldParser, QueryFieldResolver $queryFieldResolver)
    {
        $this->contentFieldParser = $contentFieldParser;
        $this->queryFieldResolver = $queryFieldResolver;
    }

    public function getContentForTwig(string $textQuery, array $parameters = [])
    {
        $schema = new Schema([
            'query' => new QueryType($this->contentFieldParser, $this->queryFieldResolver)
        ]);

        $result = GraphQL::executeQuery($schema, $textQuery);
        dump($result);die;
    }
}
