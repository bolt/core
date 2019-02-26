<?php

namespace Bolt\Storage\Query;

use Bolt\Storage\Query\Conditional\Types;
use Doctrine\ORM\Query\Expr;

class FilterExpressionBuilder
{
    private const ORX = 'OR';

    private const ANDX = 'AND';

    private $nestedFilterParams = [
        'OR', 'AND',
    ];

    public function build(array $filters)
    {
        $expr = new Expr();
        $expressions = [];
        foreach ($filters as $filterName => $filterOptions) {
            if (in_array($filterName, $this->nestedFilterParams)) {
                $this->generateNestedExpr($expressions, $filterName, reset($filterOptions));
            } else {
                $expressions[] = $this->getExpressionForField($filterName, $filterOptions);
            }
        }

        return call_user_func_array([$expr, 'andX'], $expressions);
    }

    private function generateNestedExpr(array &$expressions, string $filterName, array $filterOptions): void
    {
        $expr = new Expr();
        switch ($filterName) {
            case self::ORX:
                $orExpressions = [];
                foreach ($filterOptions as $filterField => $filterValue) {
                    if (in_array($filterField, $this->nestedFilterParams)) {
                        $this->generateNestedExpr($orExpressions, $filterName, reset($filterOptions));
                    } else {
                        $orExpressions[] = $this->getExpressionForField($filterField, $filterValue);
                    }
                }

                $expressions[] = call_user_func_array([$expr, 'orX'], $orExpressions);
                break;
            case self::ANDX:
                $andExpressions = [];
                foreach ($filterOptions as $filterField => $filterValue) {
                    if (in_array($filterField, $this->nestedFilterParams)) {
                        $this->generateNestedExpr($andExpressions, $filterName, reset($filterOptions));
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

        switch ($operation) {
            case Types::CONTAINS:
                return $expr->like($field, $fieldValue);
                break;
            case Types::NOT_CONTAINS:
                return $expr->notLike($field, $fieldValue);
                break;
            case Types::NOT:
                return $expr->neq($field, $fieldValue);
                break;
            case Types::NOT_IN:
                return $expr->notIn($field, $fieldValue);
                break;
            case Types::IN:
                return $expr->in($field, $fieldValue);
                break;
            case Types::GREATER_THAN:
                return $expr->gt($field, $fieldValue);
                break;
            case Types::GREATER_THAN_EQUAL:
                return $expr->gte($field, $fieldValue);
                break;
            case Types::LESS_THAN:
                return $expr->lt($field, $fieldValue);
                break;
            case Types::LESS_THAN_EQUAL:
                return $expr->lte($field, $fieldValue);
                break;
            default:
                return $expr->eq($field, $fieldValue);
                break;
        }
    }

    private function getFieldOperation(string $fieldName): array
    {
        $exploded = explode('_', $fieldName);

        return count($exploded) === 2 ? $exploded : [$fieldName, null];
    }
}