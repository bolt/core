<?php

namespace Bolt\Doctrine\Migrations;

use Bolt\Doctrine\Logger\DatabaseLoggerDisabler;
use Doctrine\DBAL\Connection;
use function gc_collect_cycles;
use function ini_set;
use function json_decode;
use function json_encode;
use function serialize;
use function unserialize;

class ArrayToJsonMigrator
{
    public static function migrateUp(Connection $connection, string $tableName, string $propertyName, callable $log): void
    {
        // Disable memory limit to make sure large tables are migrated successfully
        ini_set('memory_limit', -1);

        DatabaseLoggerDisabler::disableSqlLogger($connection);

        $log("Migrating {$propertyName} column in {$tableName} table from serialized PHP array to JSON");

        // First migrate the empty rows
        $emptyArray = [];
        $rowCount = $connection->executeStatement("UPDATE `{$tableName}` SET `{$propertyName}` = ? WHERE `{$propertyName}` = ?", [
            json_encode($emptyArray),
            serialize($emptyArray),
        ]);
        $log("Migrated {$rowCount} empty rows");
        $rowCount = $connection->executeStatement("UPDATE `{$tableName}` SET `{$propertyName}` = NULL WHERE `{$propertyName}` = 'N;'");
        $log("Migrated {$rowCount} null rows");

        // Then migrate the rows with data. It is filtered on the start of a PHP serialized array, so it can be resumed if the migration fails
        $rows = $connection->prepare("SELECT id, `{$propertyName}` FROM `{$tableName}` WHERE `{$propertyName}` LIKE 'a:%'");
        $i = 0;
        $updateSql = "UPDATE `{$tableName}` SET `{$propertyName}` = :propertyJson WHERE id = :id";

        $result = $rows->executeQuery();
        $rowCount = $result->rowCount();
        $log("Non-empty rows to be migrated: {$rowCount}");

        foreach ($result->iterateAssociative() as $row) {
            $id = $row['id'];
            $propertyString = $row[$propertyName];
            $propertyArray = unserialize($propertyString);
            // This is where the actual migration happens
            $propertyJson = json_encode($propertyArray);

            $connection->executeStatement($updateSql, [
                'propertyJson' => $propertyJson,
                'id' => $id,
            ]);

            $i++;

            if ($i % 100 == 0) {
                // Run garbage collector to preserve memory
                gc_collect_cycles();
                if ($i % 1000 == 0) {
                    $log("Migrated {$i} rows");
                }
            }
        }
    }

    public static function migrateDown(Connection $connection, string $tableName, string $propertyName, callable $log): void
    {
        // Disable memory limit to make sure large tables are migrated successfully
        ini_set('memory_limit', -1);

        DatabaseLoggerDisabler::disableSqlLogger($connection);

        $log("Migrating {$propertyName} column in {$tableName} table from JSON to serialized PHP array");

        // First migrate the empty rows
        $emptyArray = [];
        $rowCount = $connection->executeStatement("UPDATE `{$tableName}` SET `{$propertyName}` = ? WHERE `{$propertyName}` = ?", [
            serialize($emptyArray),
            json_encode($emptyArray),
        ]);
        $log("Migrated {$rowCount} empty rows");
        $rowCount = $connection->executeStatement("UPDATE `{$tableName}` SET `{$propertyName}` = 'N;' WHERE `{$propertyName}` IS NULL");
        $log("Migrated {$rowCount} null rows");

        // Then migrate the rows with data. It is filtered on the start of a JSON serialized array, so it can be resumed if the migration fails
        $rows = $connection->prepare("SELECT id, `{$propertyName}` FROM `{$tableName}` WHERE `{$propertyName}` LIKE '{%}' OR `{$propertyName}` LIKE '[%]'");
        $i = 0;
        $updateSql = "UPDATE `{$tableName}` SET `{$propertyName}` = :propertyString WHERE id = :id";

        $result = $rows->executeQuery();
        $rowCount = $result->rowCount();
        $log("Non-empty rows to be migrated: {$rowCount}");

        foreach ($result->iterateAssociative() as $row) {
            $id = $row['id'];
            $propertyJson = $row[$propertyName];
            $propertyArray = json_decode((string) $propertyJson, true);
            // This is where the actual migration happens
            $propertyString = serialize($propertyArray);

            $connection->executeStatement($updateSql, [
                'propertyString' => $propertyString,
                'id' => $id,
            ]);

            $i++;

            if ($i % 100 == 0) {
                // Run garbage collector to preserve memory
                gc_collect_cycles();
                if ($i % 1000 == 0) {
                    $log("Migrated {$i} rows");
                }
            }
        }
    }
}
