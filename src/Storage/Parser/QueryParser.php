<?php

namespace Bolt\Storage\Parser;

use Bolt\Configuration\Config;
use Bolt\Storage\Builder\ContentBuilder;
use Bolt\Storage\Builder\Filter\GraphFilter;
use Bolt\Storage\Builder\GraphBuilder;
use Bolt\Storage\Definition\FieldDefinition;

class QueryParser
{
    private const QUERY_START_PATTERN = '/\s*query\s*/';
    private const QUERY_END_PATTERN = '/}\s*$/';
    private const MULTIPLY_WHITESPACE_PATTERN = '/\s{2,}/';
    private const QUERY_CONTENT_PATTERN = '/([a-zA-Z0-9_]+)\s{\s*([a-zA-Z0-9_\s\*]+)\s*}/';
    private const DEPRECATED_QUERY_PATTERN = '/[a-zA-Z0-9_]+(\/[a-zA-Z0-9_\-]+)?/';

    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function parseQuery(string $query): string
    {
        $textQuery = $query;
        $contents = $fields = [];

        if ($this->isQuery($textQuery)) {
            $this->removeQueryPlaceholder($textQuery);
            $this->removeMultipleWhitespaces($textQuery);

            ['contents' => $contents, 'fields' => $fields] = $this->parseQueryContent($textQuery);
        }

        if ($this->isDeprecatedQuery($textQuery)) {
            ['contents' => $contents, 'fields' => $fields] = $this->parseDeprecatedQuery($textQuery);
        }

        foreach ($contents as $index => $content) {
            $arrayFields = array_filter(explode(' ', $fields[$index]));
            if (in_array('*', $arrayFields, true) && count($fields) === 1) {
                $textQuery = preg_replace('/\*/', $this->getAllFields($content), $textQuery, 1);
            }
        }

        return $textQuery;
    }

    private function isQuery(string $textQuery): bool
    {
        return mb_strpos(ltrim($textQuery), 'query') === 0 ||
            preg_match(self::QUERY_CONTENT_PATTERN, $textQuery) === 1;
    }

    private function isDeprecatedQuery(string $textQuery): bool
    {
        return preg_match(self::DEPRECATED_QUERY_PATTERN, $textQuery) === 1;
    }

    private function removeQueryPlaceholder(string &$textQuery): void
    {
        $textQuery = trim(
            preg_replace(
                [self::QUERY_START_PATTERN, self::QUERY_END_PATTERN],
                '',
                $textQuery
            )
        );
    }

    private function removeMultipleWhitespaces(string &$textQuery): void
    {
        $textQuery = preg_replace(self::MULTIPLY_WHITESPACE_PATTERN, ' ', $textQuery);
    }

    private function parseQueryContent(string $textQuery)
    {
        preg_match_all(self::QUERY_CONTENT_PATTERN, $textQuery, $matches);

        [, $contents, $fields] = $matches;

        return [
            'contents' => $contents,
            'fields' => $fields,
        ];
    }

    private function parseDeprecatedQuery(string &$textQuery): array
    {
        $graphBuilder = new GraphBuilder();

        [$contentType, $searchValue] = explode('/', $textQuery);

        $allFields = $this->getAllFields($contentType);
        $textQuery = $graphBuilder->addContent(
            ContentBuilder::create($contentType)
                ->selectFields($allFields)
                ->addFilter(GraphFilter::createSimpleFilter('slug', $searchValue))
        )->getQuery();
        preg_match_all(self::QUERY_CONTENT_PATTERN, $textQuery, $matches);

        [, $contents, $fields] = $matches;

        return [
            'contents' => $contents,
            'fields' => $fields,
        ];
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
        return $this->prepareFields($this->getFieldsByContentType($contentType));
    }

    private function getFieldsByContentType(string $contentType): array
    {
        return $this->config->get(sprintf('contenttypes/%s/fields', $contentType))->toArray();
    }
}
