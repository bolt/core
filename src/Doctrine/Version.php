<?php

declare(strict_types=1);

namespace Bolt\Doctrine;

use Bolt\Common\Str;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOConnection;

class Version
{
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
            $this->connection->executeQuery('SELECT 1 FROM ' . $this->tablePrefix . 'content LIMIT 1; ');
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }
}
