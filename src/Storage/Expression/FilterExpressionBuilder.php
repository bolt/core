<?php

declare(strict_types=1);

namespace Bolt\Storage\Expression;

use Bolt\Storage\Conditional\Types;
use Doctrine\ORM\Query\Expr;
use Ramsey\Uuid\Uuid;

class FilterExpressionBuilder
{
    private const ORX = 'OR';

    private const ANDX = 'AND';

    private $nestedFilterParams = [
        'OR', 'AND',
    ];

    private $table = 'bf';

    private $aliasCounter = 1;

    private $parameterNames = [];

    public function build(array $filters)
    {
        $expressions = [];
        foreach ($filters as $filterName => $filterOptions) {
            if (in_array($filterName, $this->nestedFilterParams, true)) {
                $this->generateNestedExpr($expressions, $filterName, $filterOptions);
            } else {
                $expressions[$filterName] = $this->getExpressionForField($filterName, $filterOptions);
            }
            $this->aliasCounter++;
        }

        return (new Expr())->andX(...array_values($expressions));
    }

    public function getParametersValues(): array
    {
        return $this->parameterNames;
    }

    public function getAliasCounter(): int
    {
        return $this->aliasCounter;
    }

    private function generateNestedExpr(
        array &$expressions,
        string $filterName,
        array $filterOptions
    ): void {
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
                        $orExpressions[$filterField] = $this->getExpressionForField($filterField, $filterValue);
                    }
                    $this->aliasCounter++;
                }

                $expressions[] = $expr->orX(...array_values($orExpressions));
                break;
            case self::ANDX:
                $andExpressions = [];
                foreach ($filterOptions as $filterKeyValue) {
                    $filterField = key($filterKeyValue);
                    $filterValue = $filterKeyValue[$filterField];
                    if (in_array($filterField, $this->nestedFilterParams, true)) {
                        $this->generateNestedExpr($andExpressions, $filterName, $filterOptions);
                    } else {
                        $andExpressions[$filterField] = $this->getExpressionForField($filterField, $filterValue);
                    }
                    $this->aliasCounter++;
                }
                $expressions[] = $expr->andX(...array_values($andExpressions));
                break;
        }
    }

    private function getExpressionForField(string $fieldName, $fieldValue)
    {
        $expr = new Expr();
        $alias = $this->table.$this->aliasCounter;
        $andFieldExpressions = [];

        [$field, $operation] = $this->getFieldOperation($fieldName);
        $parameterName = $this->getUniqueParameterName($field);

        $this->parameterNames[$parameterName] = $fieldValue;

        $andFieldExpressions[] = $expr->eq($alias.'.name', ':fieldName'.ucfirst($field));
        $this->parameterNames['fieldName'.ucfirst($field)] = $field;
        switch ($operation) {
            case Types::CONTAINS:
                $this->parameterNames[$parameterName] = '%'.$fieldValue.'%';
                $andFieldExpressions[] = $expr->like($alias.'.value', $parameterName);
                break;
            case Types::NOT_CONTAINS:
                $this->parameterNames[$parameterName] = '%'.$fieldValue.'%';
                $andFieldExpressions[] = $expr->notLike($alias.'.value', $parameterName);
                break;
            case Types::NOT:
                $andFieldExpressions[] = $expr->neq($alias.'.value', $parameterName);
                break;
            case Types::NOT_IN:
                $andFieldExpressions[] = $expr->notIn($alias.'.value', $parameterName);
                break;
            case Types::IN:
                $andFieldExpressions[] = $expr->in($alias.'.value', $parameterName);
                break;
            case Types::GREATER_THAN:
                $andFieldExpressions[] = $expr->gt($alias.'.value', $parameterName);
                break;
            case Types::GREATER_THAN_EQUAL:
                $andFieldExpressions[] = $expr->gte($alias.'.value', $parameterName);
                break;
            case Types::LESS_THAN:
                $andFieldExpressions[] = $expr->lt($alias.'.value', $parameterName);
                break;
            case Types::LESS_THAN_EQUAL:
                $andFieldExpressions[] = $expr->lte($alias.'.value', $parameterName);
                break;
            default:
                $andFieldExpressions[] = $expr->eq($alias.'.value', $parameterName);
                break;
        }

        return $expr->andX(...$andFieldExpressions);
    }

    private function getFieldOperation(string $fieldName): array
    {
        $exploded = explode('~', $fieldName);

        if (count($exploded) === 2) {
            return [$exploded[0], '~'.$exploded[1]];
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
