<?php

declare(strict_types=1);

namespace Bolt\Storage\Query;

use Doctrine\ORM\Query\Expr\Composite;

/**
 *  This class represents a single filter that converts to an expression along
 *  with associated query values.
 *
 *  @author Ross Riley <riley.ross@gmail.com>
 */
class Filter
{
    protected $key;
    /** @var Composite */
    protected $expression;
    /** @var array */
    protected $parameters = [];

    /**
     * Sets the key that this filter affects.
     *
     * @param string|array $key
     */
    public function setKey($key): void
    {
        $this->key = $key;
    }

    /**
     * Getter for key.
     *
     * @return string|array
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Gets the compiled expression as a string. This will look
     * something like `(alias.key = :placeholder)`.
     */
    public function getExpression(): string
    {
        return $this->expression->__toString();
    }

    /**
     * Allows replacing the expression object with a modified one.
     */
    public function setExpression(Composite $expression): void
    {
        $this->expression = $expression;
    }

    /**
     * Returns the actual object of the expression. This is generally
     * only needed for on the fly modification, to get the compiled
     * expression use getExpression().
     */
    public function getExpressionObject(): Composite
    {
        return $this->expression;
    }

    /**
     * Returns the array of parameters attached to this filter. These are
     * normally used to replace placeholders at compile time.
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Setter method to replace parameters.
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * Helper method to check if parameters are set for a specific key.
     */
    public function hasParameter(string $param): bool
    {
        return array_key_exists($param, $this->parameters);
    }

    /**
     * Allows setting a parameter for a single key.
     */
    public function setParameter(string $param, $value): void
    {
        $this->parameters[$param] = $value;
    }
}
