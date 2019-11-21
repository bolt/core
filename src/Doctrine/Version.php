<?php

declare(strict_types=1);

namespace Bolt\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOConnection;

class Version
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
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
