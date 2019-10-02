<?php

declare(strict_types=1);

namespace Bolt\Storage\Handler;

use Bolt\Storage\ContentQueryParser;
use Pagerfanta\Pagerfanta;

/**
 *  Handler to modify query based on activation of 'first' modifier.
 *
 *  eg: 'pages/first/3'
 */
class FirstQueryHandler
{
    public function __invoke(ContentQueryParser $contentQuery): Pagerfanta
    {
        $contentQuery->setDirective('order', 'id');

        return call_user_func($contentQuery->getHandler('select'), $contentQuery);
    }
}
