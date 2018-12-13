<?php

declare(strict_types=1);

namespace Bolt\Storage\Query;

use Doctrine\ORM\QueryBuilder;

/**
 * Interface that defines minimum functionality of a Bolt Query class.
 *
 * The goal of a query is to store select and filter parameters that can be
 * used to create a relevant SQL expression.
 */
interface QueryInterface
{
    /**
     * Builds the query and returns an instance of QueryBuilder.
     */
    public function build(): QueryBuilder;

    /**
     * Returns the current instance of QueryBuilder.
     */
    public function getQueryBuilder(): QueryBuilder;

    public function __toString(): string;
}
