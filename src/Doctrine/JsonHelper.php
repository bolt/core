<?php

declare(strict_types=1);

namespace Bolt\Doctrine;

use Bolt\Common\Json;
use Doctrine\DBAL\Connection;

class JsonHelper
{
    /**
     * Prepare a given $where and $slug to be used in a query, depending on
     * whether or not the current platform supports JSON functions
     *
     * For example, wrapJsonFunction('foo', 'bar') gives:
     *
     * Older SQLite, Mysql 5.6 -> [ 'foo', '["bar"]' ]
     * Newer SQLite, Mysql 5.7 -> [ "JSON_EXTRACT(foo, '$[0]')", 'bar' ]
     *
     * @param string|bool|null $slug
     *
     * @return string|array
     */
    public static function wrapJsonFunction(?string $where, $slug, Connection $connection)
    {
        $version = new Version($connection);

        if ($version->hasJson()) {
            //PostgreSQL handles JSON differently than MySQL
            if ($version->getPlatform()['driver_name'] === 'pgsql') {
                // PostgreSQL
                $resultWhere = 'JSON_GET_TEXT(' . $where . ', 0)';
            } elseif ($version->getPlatform()['driver_name'] === 'mysql') {
                // MySQL
                $resultWhere = 'JSON_UNQUOTE(JSON_EXTRACT(' . $where . ", '$[0]'))";
            } else {
                // SQLite
                $resultWhere = 'JSON_EXTRACT(' . $where . ", '$[0]')";
            }
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
}
