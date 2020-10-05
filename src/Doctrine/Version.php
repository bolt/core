<?php

declare(strict_types=1);

namespace Bolt\Doctrine;

use Bolt\Common\Str;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOConnection;
use Doctrine\DBAL\Platforms\MariaDb1027Platform;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Platforms\PostgreSQL92Platform;
use Doctrine\DBAL\Platforms\SqlitePlatform;

class Version
{
    /**
     * We're g̶u̶e̶s̶s̶i̶n̶g̶ doing empirical research on which versions of SQLite
     * support JSON. So far, tests indicate:
     * https://www.sqlite.org/json1.html --> JSON since SQLite version 3.9.0 (2015-10-14)
     * Docker uses an outdated version of SQLite/PHP combi with wrong implementation of JSON1 extensions:
     * https://www.talvbansal.me/blog/unit-tests-with-json-columns-in-sqlite/
     * This explains why BOLT is working in stand-alone production/dev envs. but not in unit tests using Docker
     * We need to replace this with a proper function test, instead of a guestimate.
     * - 3.32.2 - OK (Wytse's FBSD 12.1 \w PHP 7.2)
     * - 3.20.1 - Not OK (Travis PHP 7.2)
     * - 3.27.2 - OK (Bob's Raspberry Pi, running PHP 7.3.11 on Raspbian)
     * - 3.28.0 - OK (Travis PHP 7.3)
     * - 3.28.0 - Not OK (Bob's PHP 7.2, installed with Brew)
     * - 3.29.0 - OK (MacOS Mojave)
     * - 3.30.1 - OK (MacOS Catalina)
     */
    // JSON supported since SQLite version 3.9.0
    public const SQLITE_WITH_JSON = '3.27.0';
    // PHP supports SQLite since version 5.3.0, but not always bundled with JSON support!
    public const PHP_WITH_SQLITE_JSON_SUPPORT = '7.3.0';

    /** @var Connection */
    private $connection;

    /** @var string */
    private $tablePrefix;

    public function __construct(Connection $connection, string $tablePrefix = 'bolt')
    {
        $this->connection = $connection;
        $this->tablePrefix = Str::ensureEndsWith($tablePrefix, '_');
    }

    public function getPlatform(): array
    {
        /** @var PDOConnection $wrapped */
        $wrapped = $this->connection->getWrappedConnection();

        [$client_version] = explode(' - ', $wrapped->getAttribute(\PDO::ATTR_CLIENT_VERSION));

        try {
            $status = $wrapped->getAttribute(\PDO::ATTR_CONNECTION_STATUS);
        } catch (\PDOException $e) {
            $status = '';
        }

        return [
            'client_version' => $client_version,
            'driver_name' => $wrapped->getAttribute(\PDO::ATTR_DRIVER_NAME),
            'connection_status' => $status,
            'server_version' => $wrapped->getAttribute(\PDO::ATTR_SERVER_VERSION),
        ];
    }

    public function tableContentExists(): bool
    {
        try {
            $query = $this->connection->createQueryBuilder();
            $query
                ->select('1')
                ->from($this->tablePrefix . 'content');
            $query->execute();
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }

    public function hasJson(): bool
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof SqlitePlatform) {
            // new method to test for JSON support
            return $this->hasSQLiteJSONSupport();

            // temporarily leave this in, until above method is fully tested
            // return $this->checkSqliteVersion();
        }

        // MySQL80Platform is implicitly included with MySQL57Platform
        if ($platform instanceof MySQL57Platform || $platform instanceof MariaDb1027Platform) {
            return true;
        }

        // PostgreSQL supports JSON from v9.2 and above, later versions are implicitly included
        if ($platform instanceof PostgreSQL92Platform) {
            return true;
        }

        return false;
    }

    public function hasCast(): bool
    {
        try {
            $query = $this->connection->createQueryBuilder();
            $query
                ->select('CAST (1.1 AS int)');
            $query->execute();
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }

    /* leave until alternative method fully tested */
    // private function checkSqliteVersion(): bool
    // {
    //    /** @var PDOConnection */
    //    $wrapped = $this->connection->getWrappedConnection();

    //    // If the wrapper doesn't have `getAttribute`, we bail…
    //    if (! method_exists($wrapped, 'getAttribute')) {
    //        return false;
    //    }

    //    [$client_version] = explode(' - ', $wrapped->getAttribute(\PDO::ATTR_CLIENT_VERSION));

    //    return (version_compare($client_version, self::SQLITE_WITH_JSON) > 0) &&
    //        (version_compare(PHP_VERSION, self::PHP_WITH_SQLITE_JSON_SUPPORT) > 0);
    //}

    private function hasSQLiteJSONSupport(): bool
    {
        // Here we can test SQLite for JSON support
        // This should also work for MySQL
        // For PostgreSQL a different query is required, but this should be easy to expand (check for driver + adjust query)
        // Query = "SELECT JSON_EXTRACT('{"jsonfunctionalitytest":["succes"]}', '$.jsonfunctionalitytest') as value";
        // Should return value ["succes"], and throw an error if JSON unsupported.

        try {
            $query = $this->connection->createQueryBuilder();
            $query
                ->select('JSON_EXTRACT(\'{"jsonfunctionalitytest":["succes"]}\', \'$.jsonfunctionalitytest\') as value');
            $query->execute();
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }
}
