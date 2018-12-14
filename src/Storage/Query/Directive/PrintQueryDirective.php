<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Directive;

use Bolt\Storage\Query\QueryInterface;

/**
 *  Directive a raw output of the generated query.
 */
class PrintQueryDirective
{
    public function __invoke(QueryInterface $query): void
    {
        echo $query;
    }
}
