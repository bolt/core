<?php

declare(strict_types=1);

namespace Bolt\Storage\Directive;

use Bolt\Storage\QueryInterface;

/**
 *  Directive that allows running of a callback on query.
 */
class GetQueryDirective
{
    public const NAME = 'getquery';

    public function __invoke(QueryInterface $query, callable $callback): void
    {
        $callback($query);
    }
}
