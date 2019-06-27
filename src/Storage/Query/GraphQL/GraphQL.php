<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\GraphQL;

use Bolt\Storage\Query\GraphQL\Language\Parser;
use GraphQL\Error\Error;
use GraphQL\Executor\ExecutionResult;
use GraphQL\Executor\Executor;
use GraphQL\Executor\Promise\Adapter\SyncPromiseAdapter;
use GraphQL\Executor\Promise\Promise;
use GraphQL\Executor\Promise\PromiseAdapter;
use GraphQL\GraphQL as BaseGraphQL;
use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\Source;
use GraphQL\Type\Schema as SchemaType;
use GraphQL\Validator\DocumentValidator;
use GraphQL\Validator\Rules\QueryComplexity;

class GraphQL extends BaseGraphQL
{
    public static function executeQuery(
        SchemaType $schema,
        $source,
        $rootValue = null,
        $context = null,
        $variableValues = null,
        ?string $operationName = null,
        ?callable $fieldResolver = null,
        ?array $validationRules = null
    ): ExecutionResult {
        $promiseAdapter = new SyncPromiseAdapter();

        $promise = self::promiseToExecute(
            $promiseAdapter,
            $schema,
            $source,
            $rootValue,
            $context,
            $variableValues,
            $operationName,
            $fieldResolver,
            $validationRules
        );

        return $promiseAdapter->wait($promise);
    }

    public static function promiseToExecute(
        PromiseAdapter $promiseAdapter,
        SchemaType $schema,
        $source,
        $rootValue = null,
        $context = null,
        $variableValues = null,
        ?string $operationName = null,
        ?callable $fieldResolver = null,
        ?array $validationRules = null
    ): Promise {
        try {
            if ($source instanceof DocumentNode) {
                $documentNode = $source;
            } else {
                $documentNode = Parser::parse(new Source($source ?: '', 'GraphQL'));
            }

            // FIXME
            if (empty($validationRules)) {
                /** @var QueryComplexity $queryComplexity */
                $queryComplexity = DocumentValidator::getRule(QueryComplexity::class);
                $queryComplexity->setRawVariableValues($variableValues);
            } else {
                foreach ($validationRules as $rule) {
                    if (! ($rule instanceof QueryComplexity)) {
                        continue;
                    }

                    $rule->setRawVariableValues($variableValues);
                }
            }

            $validationErrors = DocumentValidator::validate($schema, $documentNode, $validationRules);

            if (! empty($validationErrors)) {
                return $promiseAdapter->createFulfilled(
                    new ExecutionResult(null, $validationErrors)
                );
            }

            return Executor::promiseToExecute(
                $promiseAdapter,
                $schema,
                $documentNode,
                $rootValue,
                $context,
                $variableValues,
                $operationName,
                $fieldResolver
            );
        } catch (Error $e) {
            return $promiseAdapter->createFulfilled(
                new ExecutionResult(null, [$e])
            );
        }
    }

    public static function execute(
        SchemaType $schema,
        $source,
        $rootValue = null,
        $contextValue = null,
        $variableValues = null,
        ?string $operationName = null
    ) {
        @trigger_error(
            __METHOD__ . ' is deprecated, use GraphQL::executeQuery()->toArray() as a quick replacement',
            E_USER_DEPRECATED
        );

        $promiseAdapter = Executor::getPromiseAdapter();
        $result = self::promiseToExecute(
            $promiseAdapter,
            $schema,
            $source,
            $rootValue,
            $contextValue,
            $variableValues,
            $operationName
        );

        if ($promiseAdapter instanceof SyncPromiseAdapter) {
            $result = $promiseAdapter->wait($result)->toArray();
        } else {
            $result = $result->then(static function (ExecutionResult $r) {
                return $r->toArray();
            });
        }

        return $result;
    }
}
