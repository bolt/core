<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Handler;

use Bolt\Storage\Query\ContentQueryParser;
use Bolt\Storage\Query\QueryResultset;

/**
 *  Handler to modify query based on activation of 'first' modifier.
 *
 *  eg: 'pages/first/3'
 */
class FirstQueryHandler
{
    public function __invoke(ContentQueryParser $contentQuery): QueryResultset
    {
        $contentQuery->setDirective('order', 'id');

        return call_user_func($contentQuery->getHandler('select'), $contentQuery);
    }
}
