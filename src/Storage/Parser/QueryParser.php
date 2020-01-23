<?php

namespace Bolt\Storage\Parser;

use Bolt\Configuration\Config;
use Bolt\Storage\Builder\ContentBuilder;
use Bolt\Storage\Builder\Filter\GraphFilter;
use Bolt\Storage\Builder\FilterFieldBuilder;
use Bolt\Storage\Builder\GraphBuilder;
use Bolt\Storage\Definition\FieldDefinition;
use Bolt\Storage\Exception\KeyValueComparatorsException;
use Bolt\Storage\Exception\UnsupportedQueryException;
use DateTime;

class QueryParser
{
    private const QUERY_START_PATTERN = '/\s*query\s*/';
    private const QUERY_END_PATTERN = '/}\s*$/';
    private const MULTIPLY_WHITESPACE_PATTERN = '/\s{2,}/';
    private const QUERY_CONTENT_PATTERN = '/([a-zA-Z0-9_]+)\s{\s*([a-zA-Z0-9_\s\*]+)\s*}/';
    private const DEPRECATED_QUERY_PATTERN = '/[a-zA-Z0-9_]+(\/[a-zA-Z0-9_\-]+)?/';

    private const SELECTORS = ['limit', 'order'];

    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function parseQuery(string $query, array $arguments = []): string
    {
        $textQuery = $query;
        $contents = $fields = [];

        if ($this->isQuery($textQuery)) {
            $this->removeQueryPlaceholder($textQuery);
            $this->removeMultipleWhitespaces($textQuery);

            ['contents' => $contents, 'fields' => $fields] = $this->parseQueryContent($textQuery);
        }

        if ($this->isDeprecatedQuery($textQuery)) {
            ['contents' => $contents, 'fields' => $fields] = $this->parseDeprecatedQuery(
                $textQuery,
                $arguments
            );
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

    private function parseDeprecatedQuery(string &$textQuery, array $arguments = []): array
    {
        $graphBuilder = new GraphBuilder();
        $contentType = $textQuery;
        $searchValue = null;
        $functionName = null;
        $functionParameter = 0;
        if (mb_strpos($textQuery, '/') !== false) {
            [$contentType, $searchValue, $functionName, $functionParameter] = $this->explodeQuery($textQuery);
        }

        $allFields = $this->getAllFields($contentType);
        $content = ContentBuilder::create($contentType)->selectFields($allFields);

        if (empty($arguments)) {
            switch ($functionName) {
                case 'random':
                    $content->setRandom($functionParameter);
                    break;
                case 'first':
                    $content->setFirstRecords($functionParameter);
                    break;
                case 'latest':
                    $content->setLatestRecords($functionParameter);
                    break;
            }

            if ($searchValue !== null) {
                $content->addFilter(GraphFilter::createSimpleFilter('slug', $searchValue));
            }
        }

        foreach ($arguments as $field => $value) {
            if ($this->isStatementSelector($field)) {
                switch ($field) {
                    case 'limit':
                        $content->setLimit($value);
                        break;
                    case 'order':
                        if (mb_strpos($value, ',') !== false) {
                            $orders = array_map(function($element) {
                                return trim($element);
                            }, explode(',', $value));
                            foreach ($orders as $val) {
                                $direction = 'ASC';
                                if ($val[0] === '-') {
                                    $direction = 'DESC';
                                    $val = substr($val, 1);
                                }
                                $content->addOrder($val, $direction);
                            }
                        } else {
                            $direction = 'ASC';
                            if ($value[0] === '-') {
                                $direction = 'DESC';
                                $value = substr($value, 1);
                            }
                            $content->setOrder($value, $direction);
                        }
                        break;
                }
            } elseif ($this->isDateSelector($value)) {
                [$operator, $value] = $this->parseValue($value);
                switch ($operator) {
                    case '%':
                        $fieldForFilter = FilterFieldBuilder::contains($field);
                        break;
                    case '>':
                        $fieldForFilter = FilterFieldBuilder::greaterThan($field);
                        break;
                    case '<':
                        $fieldForFilter = FilterFieldBuilder::lessThan($field);
                        break;
                    case '>=':
                        $fieldForFilter = FilterFieldBuilder::greaterThanEqual($field);
                        break;
                    case '<=':
                        $fieldForFilter = FilterFieldBuilder::lessThanEqual($field);
                        break;
                    case '=':
                    default:
                        $fieldForFilter = $field;
                        break;
                }

                $date = new DateTime($value);

                $content->addFilter(
                    GraphFilter::createSimpleFilter($fieldForFilter, $date->format('Y-m-d H:i:s'))
                );

            } else if ($this->isMultipleKeyValue($field, $value)) {
                [$operators, $keyValues, $comparator] = $this->parseMultipleKeyValue($field, $value);
                $fieldsForFilter = [];
                $values = [];
                foreach ($keyValues as $id => $keyValue) {
                    $field = key($keyValue);
                    $value = $keyValue[$field];
                    switch ($operators[$id]) {
                        case '%':
                            $fieldsForFilter[] = FilterFieldBuilder::contains($field);
                            $values[] = $value;
                            break;
                        case '>':
                            $fieldsForFilter[] = FilterFieldBuilder::greaterThan($field);
                            $values[] = $value;
                            break;
                        case '<':
                            $fieldsForFilter[] = FilterFieldBuilder::lessThan($field);
                            $values[] = $value;
                            break;
                        case '>=':
                            $fieldsForFilter[] = FilterFieldBuilder::greaterThanEqual($field);
                            $values[] = $value;
                            break;
                        case '<=':
                            $fieldsForFilter[] = FilterFieldBuilder::lessThanEqual($field);
                            $values[] = $value;
                            break;
                        case '=':
                        default:
                            $fieldsForFilter[] = $field;
                            break;
                    }
                }

                switch ($comparator) {
                    case '&&&':
                        $content->addFilter(GraphFilter::createAndFilter(
                            GraphFilter::createSimpleFilter($fieldsForFilter[0], $values[0]),
                            GraphFilter::createSimpleFilter($fieldsForFilter[1], $values[1])
                        ));
                        break;
                    case '|||':
                        $content->addFilter(GraphFilter::createOrFilter(
                            GraphFilter::createSimpleFilter($fieldsForFilter[0], $values[0]),
                            GraphFilter::createSimpleFilter($fieldsForFilter[1], $values[1])
                        ));
                        break;
                }
            } else if ($this->isSingleValue($value)) {
                [$operator, $value] = $this->parseValue($value);
                switch ($operator) {
                    case '%':
                        $fieldForFilter = FilterFieldBuilder::contains($field);
                        break;
                    case '>':
                        $fieldForFilter = FilterFieldBuilder::greaterThan($field);
                        break;
                    case '<':
                        $fieldForFilter = FilterFieldBuilder::lessThan($field);
                        break;
                    case '>=':
                        $fieldForFilter = FilterFieldBuilder::greaterThanEqual($field);
                        break;
                    case '<=':
                        $fieldForFilter = FilterFieldBuilder::lessThanEqual($field);
                        break;
                    case '=':
                    default:
                        $fieldForFilter = $field;
                        break;
                }
                $content->addFilter(GraphFilter::createSimpleFilter($fieldForFilter, $value));
            } else {
                [$operators, $values, $comparator] = $this->parseMultipleValue($value);
                $fieldsForFilter = [];
                foreach ($values as $id => $val) {
                    switch ($operators[$id]) {
                        case '%':
                            $fieldsForFilter[] = FilterFieldBuilder::contains($field);
                            break;
                        case '>':
                            $fieldsForFilter[] = FilterFieldBuilder::greaterThan($field);
                            break;
                        case '<':
                            $fieldsForFilter[] = FilterFieldBuilder::lessThan($field);
                            break;
                        case '>=':
                            $fieldsForFilter[] = FilterFieldBuilder::greaterThanEqual($field);
                            break;
                        case '<=':
                            $fieldsForFilter[] = FilterFieldBuilder::lessThanEqual($field);
                            break;
                        case '=':
                        default:
                            $fieldsForFilter[] = $field;
                            break;
                    }
                }

                switch ($comparator) {
                    case '&&':
                        $content->addFilter(GraphFilter::createAndFilter(
                            GraphFilter::createSimpleFilter($fieldsForFilter[0], $values[0]),
                            GraphFilter::createSimpleFilter($fieldsForFilter[1], $values[1])
                        ));
                        break;
                    case '||':
                        $content->addFilter(GraphFilter::createOrFilter(
                            GraphFilter::createSimpleFilter($fieldsForFilter[0], $values[0]),
                            GraphFilter::createSimpleFilter($fieldsForFilter[1], $values[1])
                        ));
                        break;
                }
            }
        }

        $textQuery = $graphBuilder->addContent($content)->getQuery();

        preg_match_all(self::QUERY_CONTENT_PATTERN, $textQuery, $matches);

        [, $contents, $fields] = $matches;

        return [
            'contents' => $contents,
            'fields' => $fields,
        ];
    }

    private function isStatementSelector(string $key): bool
    {
        return in_array($key, self::SELECTORS);
    }

    private function isDateSelector(string $value): bool
    {
        return strtotime($value) !== false;
    }

    private function isSingleValue(string $value): bool
    {
        return preg_match('/\|{2}|\&{2}/', $value) === 0;
    }

    private function isMultipleKeyValue(string $key, string $value): bool
    {
        return preg_match('/\|{3}|\&{3}/', $key) && preg_match('/\|{3}|\&{3}/', $value);
    }

    private function parseValue(string $value): array
    {
        preg_match('/^([\<|\>\%]?=?)/', $value, $matches);

        if (empty($matches[0])) {
            return [null, $value];
        }

        return [$matches[0], mb_substr($value, mb_strlen($matches[0]))];
    }

    private function parseMultipleValue(string $value): array
    {
        preg_match('/^([\<|\>\%]?=?)(.[^\s|&]*)\s*(\|{2}|\&{2})\s*([\<|\>\%]?=?)(.*)$/', $value, $matches);
        [, $operatorFieldOne, $valueOne, $valueComparator, $operatorFieldTwo, $valueTwo] = $matches;

        return [[$operatorFieldOne, $operatorFieldTwo], [$valueOne, $valueTwo], $valueComparator];
    }

    private function parseMultipleKeyValue(string $key, string $value): array
    {
        preg_match('/^(.[^\s|&]*)\s*(\|{3}|\&{3})\s*(.*)$/', $key, $keyMatches);
        preg_match(
            '/^([\<|\>\%]?=?)(.[^\s|&]*)\s*(\|{3}|\&{3})\s*([\<|\>\%]?=?)(.*)$/',
            $value,
            $valueMatches
        );

        [, $fieldOne, $keyComparator, $fieldTwo] = $keyMatches;
        [, $operatorFieldOne, $valueOne, $valueComparator, $operatorFieldTwo, $valueTwo] = $valueMatches;

        if ($keyComparator !== $valueComparator) {
            throw new KeyValueComparatorsException();
        }

        return [
            [$operatorFieldOne, $operatorFieldTwo],
            [
                [$fieldOne => $valueOne],
                [$fieldTwo => $valueTwo],
            ],
            $keyComparator
        ];
    }

    private function explodeQuery(string $textQuery): array
    {
        $elements = explode('/', $textQuery);
        switch (count($elements)) {
            case 2:
                return [
                    $elements[0], $elements[1], null, null
                ];
                break;
            case 3:
                return [
                    $elements[0], null, $elements[1], $elements[2]
                ];
                break;
        }

        throw new UnsupportedQueryException($textQuery);
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
