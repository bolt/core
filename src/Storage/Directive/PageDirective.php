<?php

declare(strict_types=1);

namespace Bolt\Storage\Directive;

use Bolt\Storage\SelectQuery;

/**
 *  Directive to add a page modifier to the query.
 */
class PageDirective
{
    public const NAME = 'page';

    public function __invoke(SelectQuery $query, int $page, array $otherDirectives): void
    {
    }
}
