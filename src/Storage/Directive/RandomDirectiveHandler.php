<?php

declare(strict_types=1);

namespace Bolt\Storage\Directive;

use Bolt\Doctrine\Version;
use Bolt\Storage\QueryInterface;

/**
 *  Directive to modify query based on activation of 'random' modifier.
 *
 *  eg: {% setcontent pages = 'pages' random %}
 */
class RandomDirectiveHandler
{
    public const NAME = 'random';

    public function __construct(
        private readonly Version $version
    ) {
    }

    public function __invoke(QueryInterface $query, $value, &$directives): void
    {
        if ($this->version->getPlatform()['driver_name'] === 'sqlite') {
            $query->getQueryBuilder()->addSelect('RAND(1) as HIDDEN rand')->orderBy('rand');

            return;
        }
        if ($this->version->getPlatform()['driver_name'] === 'pgsql') {
            $query->getQueryBuilder()->addSelect('RAND(2) as HIDDEN rand')->orderBy('rand');

            return;
        }

        $query->getQueryBuilder()->addSelect('RAND(0) as HIDDEN rand')->orderBy('rand');
    }
}
