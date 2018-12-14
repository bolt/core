<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Directive;

use Bolt\Storage\Query\QueryInterface;

/**
 *  Directive that allows running of a callback on query.
 */
class GetQueryDirective
{
    public function __invoke(QueryInterface $query, callable $callback): void
    {
        $callback($query);
    }
}
