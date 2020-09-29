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

    /** @var Version */
    private $version;

    public function __construct(Version $version)
    {
        $this->version = $version;
    }

    public function __invoke(QueryInterface $query, $value, &$directives): void
    {
        if ($this->version->getPlatform()['driver_name'] === 'sqlite') {
            $query->getQueryBuilder()->addSelect('RAND(1) as HIDDEN rand')->orderBy('rand');

            return;
        } if ($this->version->getPlatform()['driver_name'] === 'pgsql') {
            $query->getQueryBuilder()->addSelect('RAND(2) as HIDDEN rand')->orderBy('rand');
            //$debug_msg = $query->getQueryBuilder()->getQuery()->getSQL();
            //throw new Exception($debug_msg);
            return;
        }

        $query->getQueryBuilder()->addSelect('RAND(0) as HIDDEN rand')->orderBy('rand');
    }
}
