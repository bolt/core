<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Expression;

use Bolt\Storage\Query\Conditional\Types;
use Doctrine\ORM\Query\Expr;
use Ramsey\Uuid\Uuid;

class FilterExpressionBuilder
{
    private const ORX = 'OR';

    private const ANDX = 'AND';

    private $nestedFilterParams = [
        'OR', 'AND',
    ];

    private $table;

    private $parameterNames = [];

    public function build(string $table, array $filters)
    {
        $this->table = $table;
        $expr = new Expr();
        $expressions = [];
        foreach ($filters as $filterName => $filterOptions) {
            if (in_array($filterName, $this->nestedFilterParams, true)) {
                $this->generateNestedExpr($expressions, $filterName, $filterOptions);
            } else {
                $expressions[] = $this->getExpressionForField($filterName, $filterOptions);
            }
        }

        return call_user_func_array([$expr, 'andX'], $expressions);
    }

    public function getParametersValues(): array
    {
        return $this->parameterNames;
    }

    private function generateNestedExpr(array &$expressions, string $filterName, array $filterOptions): void
    {
        $expr = new Expr();
        switch ($filterName) {
            case self::ORX:
                $orExpressions = [];
                foreach ($filterOptions as $filterKeyValue) {
                    $filterField = key($filterKeyValue);
                    $filterValue = $filterKeyValue[$filterField];
                    if (in_array($filterField, $this->nestedFilterParams, true)) {
                        $this->generateNestedExpr($orExpressions, $filterName, reset($filterOptions));
                    } else {
                        $orExpressions[] = $this->getExpressionForField($filterField, $filterValue);
                    }
                }

                $expressions[] = call_user_func_array([$expr, 'orX'], $orExpressions);
                break;
            case self::ANDX:
                $andExpressions = [];
                foreach ($filterOptions as $filterKeyValue) {
                    $filterField = key($filterKeyValue);
                    $filterValue = $filterKeyValue[$filterField];
                    if (in_array($filterField, $this->nestedFilterParams, true)) {
                        $this->generateNestedExpr($andExpressions, $filterName, $filterOptions);
                    } else {
                        $andExpressions[] = $this->getExpressionForField($filterField, $filterValue);
                    }
                }
                $expressions[] = call_user_func_array([$expr, 'andX'], $andExpressions);
                break;
        }
    }

    private function getExpressionForField(string $fieldName, $fieldValue)
    {
        $expr = new Expr();
        [$field, $operation] = $this->getFieldOperation($fieldName);
        $parameterName = $this->getUniqueParameterName($field);
        $this->parameterNames[$parameterName] = $fieldValue;
        $andFieldExpressions = [];
        $andFieldExpressions[] = $expr->eq($this->table.'.name', "'".$field."'");
        switch ($operation) {
            case Types::CONTAINS:
                $this->parameterNames[$parameterName] = '%'.$fieldValue.'%';
                $andFieldExpressions[] = $expr->like($this->table.'.value', $parameterName);
                break;
            case Types::NOT_CONTAINS:
                $this->parameterNames[$parameterName] = '%'.$fieldValue.'%';
                $andFieldExpressions[] = $expr->notLike($this->table.'.value', $parameterName);
                break;
            case Types::NOT:
                $andFieldExpressions[] = $expr->neq($this->table.'.value', $parameterName);
                break;
            case Types::NOT_IN:
                $andFieldExpressions[] = $expr->notIn($this->table.'.value', $parameterName);
                break;
            case Types::IN:
                $andFieldExpressions[] = $expr->in($this->table.'.value', $parameterName);
                break;
            case Types::GREATER_THAN:
                $andFieldExpressions[] = $expr->gt($this->table.'.value', $parameterName);
                break;
            case Types::GREATER_THAN_EQUAL:
                $andFieldExpressions[] = $expr->gte($this->table.'.value', $parameterName);
                break;
            case Types::LESS_THAN:
                $andFieldExpressions[] = $expr->lt($this->table.'.value', $parameterName);
                break;
            case Types::LESS_THAN_EQUAL:
                $andFieldExpressions[] = $expr->lte($this->table.'.value', $parameterName);
                break;
            default:
                $andFieldExpressions[] = $expr->eq($this->table.'.value', $parameterName);
                break;
        }

        return call_user_func_array([$expr, 'andX'], $andFieldExpressions);
    }

    private function getFieldOperation(string $fieldName): array
    {
        $exploded = explode('_', $fieldName);

        if (count($exploded) === 2) {
            return [$exploded[0], '_'.$exploded[1]];
        }

        return [$fieldName, null];
    }

    private function getUniqueParameterName(string $fieldName): string
    {
        $parameterName = ':'.$fieldName;
        $uniqueValue = mb_substr(Uuid::uuid4()->toString(), 0, 5);

        return $parameterName.'_'.$uniqueValue;
    }
}
