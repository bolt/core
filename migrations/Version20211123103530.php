<?php

declare(strict_types=1);

namespace Bolt\DoctrineMigrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Psr\Log\LoggerInterface;

final class Version20211123103530 extends AbstractMigration
{
    /** @var string */
    private $tablePrefix = 'bolt';

    public function getDescription(): string
    {
        return 'Bolt 5.1 Migration: bolt_user_auth_token.user_id is not a unique constraint.';
    }

    public function up(Schema $schema): void
    {
       // Drop the UNIQUE constraint for bolt_user_auth_token.user_id. See #2912.
        $userTable = $schema->getTable($this->tablePrefix . '_user_auth_token');
        $indexes = $userTable->getIndexes();

        // Add index needed by foreign key before remove unique index
        $hasIndex = false;
        foreach ($indexes as $index) {
            if ($index->getName() === 'IDX_8B90D313A76ED395') {
                $hasIndex = true;
            }
        }

        // If no index found, add it
        if (!$hasIndex) {
            $userTable->addIndex(['user_id'], 'IDX_8B90D313A76ED395');
        }

        // Remove unique index if found
        foreach($indexes as $index) {
            if ($index->getColumns() === [0 => 'user_id'] && $index->isUnique()) {
                $userTable->dropIndex($index->getName());
                break;
            }
        }
    }

    public function down(Schema $schema): void
    {
        // No down necessary
    }
}
