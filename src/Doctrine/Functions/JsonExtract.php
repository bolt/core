<?php

declare(strict_types=1);

namespace Bolt\Doctrine\Functions;

use Doctrine\ORM\Query\SqlWalker;
use Scienta\DoctrineJsonFunctions\Query\AST\Functions\AbstractJsonFunctionNode;

/**
 * "JSON_EXTRACT" "(" StringPrimary "," StringPrimary {"," StringPrimary }* ")"
 *
 * See: "JSON_EXTRACT: Bolt\Doctrine\Functions\JsonExtract" in `config/packages/doctrine.yaml`
 */
class JsonExtract extends AbstractJsonFunctionNode
{
    public const FUNCTION_NAME = 'JSON_EXTRACT';

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
