<?php

declare(strict_types=1);

namespace Bolt\Doctrine\Functions;

use Doctrine\ORM\Query\SqlWalker;
use Scienta\DoctrineJsonFunctions\Query\AST\Functions\AbstractJsonFunctionNode;

/**
 * "JSON_UNQUOTE" "(" StringPrimary ")"
 *
 * See: "JSON_UNQUOTE: Bolt\Doctrine\Functions\JsonUnquote" in `config/packages/doctrine.yaml`
 */
class JsonUnquote extends AbstractJsonFunctionNode
{
    public const FUNCTION_NAME = 'JSON_UNQUOTE';

    /** @var string[] */
    protected $requiredArgumentTypes = [self::STRING_PRIMARY_ARG];

    /** @var string[] */
    protected $optionalArgumentTypes = [];

    /** @var bool */
    protected $allowOptionalArgumentRepeat = true;

    protected function validatePlatform(SqlWalker $sqlWalker): void
    {
    }
}
