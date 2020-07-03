<?php

declare(strict_types=1);

namespace Bolt\Storage\Directive;

use Bolt\Storage\QueryInterface;

/**
 *  Directive to modify query based on activation of 'latest' modifier.
 *
 *  eg: {% setcontent pages = 'pages' first %}
 */
class EarliestDirectiveHandler
{
    public const NAME = 'earliest';

    public function __invoke(QueryInterface $query, $value, &$directives): void
    {
        $directives[OrderDirective::NAME] = 'id';
    }
}
