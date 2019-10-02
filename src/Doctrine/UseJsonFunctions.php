<?php

declare(strict_types=1);

namespace Bolt\Doctrine;

use Doctrine\DBAL\Platforms\MariaDb1027Platform;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\QueryBuilder;

class UseJsonFunctions
{
    /**
     * Because Mysql 5.6 and Sqlite handle values in JSON differently, we
     * use this method to check if we can use JSON functions directly.
     */
    public static function check(QueryBuilder $qb): bool
    {
        $platform = $qb->getEntityManager()->getConnection()->getDatabasePlatform();

        if ($platform instanceof SqlitePlatform) {
            // @todo We need to determine somehow if SQLite was loaded with the JSON1 extension.
            return false;
        }

        if ($platform instanceof MySQL57Platform || $platform instanceof MySQL80Platform || $platform instanceof MariaDb1027Platform) {
            return true;
        }

        return false;
    }
}
