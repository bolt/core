<?php

declare(strict_types=1);

namespace Bolt\Doctrine;

use Bolt\Common\Json;
use Doctrine\DBAL\Driver\PDOConnection;
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
//            dump(self::checkSqliteVersion($qb));
            return false;
        }

        // MySQL80Platform is implicitly included with MySQL57Platform
        if ($platform instanceof MySQL57Platform || $platform instanceof MariaDb1027Platform) {
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
     *
     * @return string|array
     */
    public static function wrapJsonFunction(?string $where = null, ?string $slug = null, QueryBuilder $qb)
    {
        if (self::useJsonFunction($qb)) {
            $resultWhere = 'JSON_EXTRACT(' . $where . ", '$[0]')";
            $resultSlug = $slug;
        } else {
            $resultWhere = $where;
            $resultSlug = Json::json_encode([$slug]);
        }

        if ($where === null) {
            return $resultSlug;
        }

        if ($slug === null) {
            return $resultWhere;
        }

        return [$resultWhere, $resultSlug];
    }

    private static function checkSqliteVersion(QueryBuilder $qb): bool
    {
        /** @var PDOConnection $wrapped */
        $wrapped = $qb->getEntityManager()->getConnection()->getWrappedConnection();

        // If the wrapper doesn't have `getAttribute`, we bail…
        if (! method_exists($wrapped, 'getAttribute')) {
            return false;
        }

        [$client_version] = explode(' - ', $wrapped->getAttribute(\PDO::ATTR_CLIENT_VERSION));

        return version_compare($client_version, '3.9.0') > 0;
    }
}
