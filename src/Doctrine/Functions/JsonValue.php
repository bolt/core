<?php

declare(strict_types=1);

namespace Bolt\Doctrine\Functions;

use Doctrine\ORM\Query\SqlWalker;
use Scienta\DoctrineJsonFunctions\Query\AST\Functions\AbstractJsonFunctionNode;

/**
 * "JSON_VALUE" "(" StringPrimary "," StringPrimary {"," StringPrimary }* ")"
 *
 * See: "JSON_VALUE: Bolt\Doctrine\Functions\JsonValue" in `config/packages/doctrine.yaml`
 */
class JsonValue extends AbstractJsonFunctionNode
{
    public const FUNCTION_NAME = 'JSON_VALUE';

    /** @var string[] */
    protected $requiredArgumentTypes = [self::STRING_PRIMARY_ARG, self::STRING_PRIMARY_ARG];

    /** @var string[] */
    protected $optionalArgumentTypes = [self::STRING_PRIMARY_ARG];

    /** @var bool */
    protected $allowOptionalArgumentRepeat = true;

    protected function validatePlatform(SqlWalker $sqlWalker): void
    {
    }
}
