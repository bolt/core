<?php

declare(strict_types=1);

namespace Bolt\Doctrine;

use Bolt\Common\Str;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Exception;
use PDO;
use PDOException;
use Throwable;

readonly class Version
{
    private string $tablePrefix;

    /**
     * @param string|array{default: string} $tablePrefix
     */
    public function __construct(
        private Connection $connection,
        array|string $tablePrefix = 'bolt'
    ) {
        $tablePrefix = is_array($tablePrefix) ? $tablePrefix['default'] : $tablePrefix;
        $this->tablePrefix = Str::ensureEndsWith($tablePrefix, '_');
    }

    /**
     * @throws Exception
     *
     * @return array{
     *     client_version: string,
     *     driver_name: string,
     *     connection_status: string,
     *     server_version: string,
     * }
     */
    public function getPlatform(): array
    {
        $nativeConnection = $this->connection->getNativeConnection();

        if ($nativeConnection instanceof PDO) {
            [$client_version] = explode(' - ', (string) $nativeConnection->getAttribute(PDO::ATTR_CLIENT_VERSION));

            try {
                $status = $nativeConnection->getAttribute(PDO::ATTR_CONNECTION_STATUS);
            } catch (PDOException) {
                $status = '';
            }

            return [
                'client_version' => $client_version,
                'driver_name' => $nativeConnection->getAttribute(PDO::ATTR_DRIVER_NAME),
                'connection_status' => $status,
                'server_version' => $nativeConnection->getAttribute(PDO::ATTR_SERVER_VERSION),
            ];
        }

        throw new Exception("Native connection is not an instanceof \PDO");
    }

    public function tableContentExists(): bool
    {
        try {
            $query = $this->connection->createQueryBuilder();
            $query
                ->select('1')
                ->from($this->tablePrefix . 'content');
            $query->executeQuery();
        } catch (Throwable) {
            return false;
        }

        return true;
    }

    public function hasJson(): bool
    {
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof SQLitePlatform) {
            return $this->hasSQLiteJSONSupport();
        }

        // Corner case where MySQL platform is not specialized.
        // Was observed with deployment to platform.sh using oracle-mysql service.
        if ($platform instanceof MySQLPlatform) {
            // samples:
            // 8.0.29
            // 8.0.27-cluster
            // 10.7.3-MariaDB-1:10.7.3+maria~focal
            $serverVersion = $this->getPlatform()['server_version'];

            if (! preg_match("/^\d+\.\d+\.\d+/", (string) $serverVersion, $matches)) {
                // should throw an error or something?
                return false;
            }

            $actVersion = $matches[0];

            $isMariaDb = is_int(mb_stripos((string) $serverVersion, 'maria'));
            $minVersion = $isMariaDb
                ? '10.2.7'  // taken from MariaDb1027Platform docs
                : '5.7.9' // taken from MySQL57Platform docs
            ;

            return version_compare($actVersion, $minVersion, '>=');
        }

        // All supported PostgreSQL versions supports JSON
        if ($platform instanceof PostgreSQLPlatform) {
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
            $query->executeQuery();
        } catch (Throwable) {
            try {
                $query = $this->connection->createQueryBuilder();
                // Postgres
                $query
                    ->select('CAST(1.1 AS DOUBLE)');
                $query->executeQuery();
            } catch (Throwable) {
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
                ->select('JSON_EXTRACT("{}", "one", "")');
            $query->executeQuery();
        } catch (Throwable) {
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
            $query->executeQuery();
        } catch (Throwable) {
            return false;
        }

        return true;
    }
}
