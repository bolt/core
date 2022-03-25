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
    /** @var Connection */
    private $connection;

    /** @var string */
    private $tablePrefix;

    public function __construct(Connection $connection, $tablePrefix = 'bolt')
    {
        $this->connection = $connection;
        $tablePrefix = is_array($tablePrefix) ? $tablePrefix['default'] : $tablePrefix;
        $this->tablePrefix = Str::ensureEndsWith($tablePrefix, '_');
    }

    /**
     * @throws \Exception
     */
    public function getPlatform(): array
    {
        $wrapped = $this->connection->getWrappedConnection();

        // if the wrapped connection has itself a wrapped connection, use that one, etc.
        // This is the case in phpunit tests that use the dama/doctrine-test-bundle functionality
        while (true) {
            if (method_exists($wrapped, 'getWrappedConnection')) {
                $nextLevel = $wrapped->getWrappedConnection();
                if ($nextLevel) {
                    $wrapped = $nextLevel;
                } else {
                    break;
                }
            } else {
                break;
            }
        }

        if ($wrapped instanceof \PDO) {
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

        throw new \Exception("Wrapped connection is not an instanceof \PDO");
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
            return $this->hasSQLiteJSONSupport();
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
            // MySQL & SQLite
            $query
                ->select('CAST(1.1 AS DECIMAL)');
            $query->execute();
        } catch (\Throwable $e) {
            try {
                $query = $this->connection->createQueryBuilder();
                // Postgree
                $query
                    ->select('CAST(1.1 AS DOUBLE)');
                $query->execute();
            } catch (\Throwable $e) {
                return false;
            }
        }

        return true;
    }

    public function hasJsonSearch(): bool
    {
        try {
            $query = $this->connection->createQueryBuilder();
            $query
                ->select('JSON_SEARCH("{}", "one", "")');
            $query->execute();
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }

    /**
     * If we're using SQLite, this method tests for JSON support
     *
     * This query should return `["succes"]` or throw an error if JSON is unsupported.
     * `SELECT JSON_EXTRACT('{"jsonfunctionalitytest":["succes"]}', '$.jsonfunctionalitytest') as value;`
     */
    private function hasSQLiteJSONSupport(): bool
    {
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
