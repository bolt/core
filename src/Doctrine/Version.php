<?php

declare(strict_types=1);

namespace Bolt\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOConnection;
use Doctrine\DBAL\Platforms\MariaDb1027Platform;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\QueryBuilder;

class Version
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

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
}
