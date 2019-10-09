<?php

declare(strict_types=1);

namespace Bolt\Doctrine;

use Bolt\Common\Json;
use Doctrine\DBAL\Platforms\MariaDb1027Platform;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\QueryBuilder;

class JsonHelper
{
    /**
     * Because Mysql 5.6 and Sqlite handle values in JSON differently, we
     * use this method to check if we can use JSON functions directly.
     */
    public static function useJsonFunction(QueryBuilder $qb): bool
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

    /**
     * Prepare a given $where and $slug to be used in a query, depending on
     * whether or not the current platform supports JSON functions
     *
     * For example, wrapJsonFunction('foo', 'bar') gives:
     *
     * Sqlite, Mysql 5.6 -> [ 'foo', '["bar"]' ]
     * Mysql 5.7 -> [ "JSON_EXTRACT(foo, '$[0]')", 'bar' ]
     */
    public static function wrapJsonFunction(string $where, string $slug, QueryBuilder $qb): array
    {
        if (self::useJsonFunction($qb)) {
            $where = 'JSON_EXTRACT(' . $where . ", '$[0]')";
        } else {
            $slug = Json::json_encode([$slug]);
        }

        return [$where, $slug];
    }
}
