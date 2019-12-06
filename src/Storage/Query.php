<?php

declare(strict_types=1);

namespace Bolt\Storage;

use Bolt\Configuration\Config;
use Bolt\Storage\Definition\FieldDefinition;
use Bolt\Storage\GraphQL\GraphQL;
use Bolt\Storage\Parser\ContentFieldParser;
use Bolt\Storage\Resolver\QueryFieldResolver;
use Bolt\Storage\Scope\ScopeEnum;
use Bolt\Storage\Types\QueryType;
use GraphQL\Type\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class Query
{
    private $contentFieldParser;

    private $queryFieldResolver;

    private $config;

    public function __construct(
        ContentFieldParser $contentFieldParser,
        QueryFieldResolver $queryFieldResolver,
        Config $config
    ) {
        $this->contentFieldParser = $contentFieldParser;
        $this->queryFieldResolver = $queryFieldResolver;
        $this->config = $config;
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
        $result = GraphQL::executeQuery($schema, $this->prepareTextQuery($textQuery));

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
        $result = GraphQL::executeQuery($schema, $this->prepareTextQuery($textQuery));

        return new JsonResponse($result->toArray());
    }

    private function prepareTextQuery(string $textQuery): string
    {
        preg_match_all('/^\s*(query)?\s*{([a-zA-Z0-9_\s{}\*]+)}\s*$/', $textQuery, $matches);
        $trimmed = preg_replace('/\s{2,}/', ' ', trim(reset($matches[2])));
        preg_match_all('/([a-zA-Z0-9_]+)\s{\s*([a-zA-Z0-9_\s\*]+)\s*}/', $trimmed, $matches);

        $contents = $matches[1];
        $fields = $matches[2];

        foreach ($contents as $index => $content) {
            $arrayFields = array_filter(explode(' ', $fields[$index]));
            if (in_array('*', $arrayFields, true) && count($fields) === 1) {
                $textQuery = preg_replace(
                    '/\*/',
                    $this->prepareFields(
                        $this->config->get(
                            sprintf(
                                'contenttypes/%s/fields',
                                $content
                            )
                        )->toArray()
                    ),
                    $textQuery,
                    1
                );
            }
        }

        return $textQuery;
    }

    private function prepareFields(array $fields): string
    {
        $preparedFields = [];

        foreach ($fields as $fieldName => $fieldDefinition) {
            if (isset(FieldDefinition::SUB_FIELDS[$fieldDefinition['type']])) {
                $preparedFields[] = sprintf(
                    '%s { %s }',
                    $fieldName,
                    implode(
                        ' ',
                        FieldDefinition::SUB_FIELDS[$fieldDefinition['type']]
                    )
                );
            } else {
                $preparedFields[] = $fieldName;
            }
        }

        return implode(
            ' ',
            $preparedFields
        );
    }
}
