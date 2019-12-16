<?php

declare(strict_types=1);

namespace Bolt\Storage;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Storage\Builder\ContentBuilder;
use Bolt\Storage\Builder\Filter\GraphFilter;
use Bolt\Storage\Builder\GraphBuilder;
use Bolt\Storage\Definition\FieldDefinition;
use Bolt\Storage\Parser\ContentFieldParser;
use Bolt\Storage\Resolver\QueryFieldResolver;
use Bolt\Storage\Scope\ScopeEnum;
use Bolt\Storage\Types\QueryType;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use ReflectionClass;
use Symfony\Component\HttpFoundation\JsonResponse;

class Query
{
    /**
     * @var ContentFieldParser
     */
    private $contentFieldParser;

    /**
     * @var QueryFieldResolver
     */
    private $queryFieldResolver;

    /**
     * @var Config
     */
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

    public function getContentForTwig(string $textQuery): array
    {
        $schema = new Schema([
            'query' => new QueryType(
                $this->contentFieldParser,
                $this->queryFieldResolver,
                ScopeEnum::FRONT
            ),
        ]);
        $textQuery = $this->prepareTextQuery($textQuery);
        $result = GraphQL::executeQuery($schema, $textQuery);

        $content = reset($result->toArray()['data']);

        return reset($content);
    }

    private function prepareTextQuery(string $textQuery): string
    {
        preg_match_all('/^\s*(query)?\s*{([a-zA-Z0-9_\s{}\*]+)}\s*$/', $textQuery, $matches);

        $trimmed = $textQuery;
        if (empty($matches[2]) === false) {
            $trimmed = preg_replace('/\s{2,}/', ' ', trim(reset($matches[2])));
        }
        preg_match_all('/([a-zA-Z0-9_]+)\s{\s*([a-zA-Z0-9_\s\*]+)\s*}/', $trimmed, $matches);

        if (empty($matches[0]) && preg_match('#[a-zA-Z0-9_]+(\/[a-zA-Z0-9_\-]+)?#', $textQuery)) {
            $graphBuilder = new GraphBuilder();
            [$contentType, $searchValue] = explode('/', $textQuery);

            $allFields = $this->getAllFields($contentType);
            $textQuery = $graphBuilder->addContent(
                ContentBuilder::create($contentType)
                    ->selectFields($allFields)
                    ->addFilter(GraphFilter::createSimpleFilter('slug', $searchValue))
            )->getQuery();

            preg_match_all('/([a-zA-Z0-9_]+)\s{\s*([a-zA-Z0-9_\s\*]+)\s*}/', $textQuery, $matches);
        }

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

    private function getAllFields(string $contentType): string
    {
        $fields = $this->config->get(
            sprintf(
                'contenttypes/%s/fields',
                $contentType
            )
        )->toArray();

        return $this->prepareFields($fields);
    }
}
