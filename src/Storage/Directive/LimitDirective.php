<?php

declare(strict_types=1);

namespace Bolt\Storage\Directive;

use Bolt\Storage\QueryInterface;

/**
 *  Directive to add a limit modifier to the query.
 */
class LimitDirective
{
    public const NAME = 'limit';

    public function __invoke(QueryInterface $query, int $limit): void
    {
        $query->getQueryBuilder()->setMaxResults($limit);
    }
}
