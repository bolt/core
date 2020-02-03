<?php

namespace Bolt\Storage\Strategy\Traits;

use Bolt\Storage\Builder\FilterFieldBuilder;

trait SingleValueParserTrait
{
    protected function parseValue(string $value): array
    {
        preg_match('/^([\<|\>\%]?=?)/', $value, $matches);

        if (empty($matches[0])) {
            return [null, $value];
        }

        return [$matches[0], mb_substr($value, mb_strlen($matches[0]))];
    }

    protected function getFieldForFilter(string $operator, $field): string
    {
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

        return $fieldForFilter;
    }
}