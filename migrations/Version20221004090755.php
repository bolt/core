<?php

declare(strict_types=1);

namespace Bolt\DoctrineMigrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Psr\Log\LoggerInterface;

final class Version20221004090755 extends AbstractMigration
{
    /** @var string */
    private $tablePrefix = 'bolt';

    public function getDescription() : string
    {
        return 'Bolt 5.2 Migration: Add column for user about';
    }

    public function up(Schema $schema): void
    {
        // Create the user 'about'. See https://github.com/bolt/core/pull/3327
        $userTable = $schema->getTable($this->tablePrefix . '_user');

        if (! $userTable->hasColumn('about')) {
            $userTable->addColumn('about', 'string', ['notnull' => false, 'length' => 1024]);
        }

        $contentTable = $schema->getTable($this->tablePrefix . '_content');

        if (! $contentTable->hasColumn('title')) {
            $contentTable->addColumn('title', 'string', ['notnull' => false, 'length' => 191]);
        }
        if (! $contentTable->hasColumn('list_format')) {
            $contentTable->addColumn('list_format', 'string', ['notnull' => false, 'length' => 191]);
        }

    }

    public function down(Schema $schema): void
    {
        $userTable = $schema->getTable($this->tablePrefix . '_user');

        $userTable->dropColumn('about');

        $contentTable = $schema->getTable($this->tablePrefix . '_content');

        $contentTable->dropColumn('title');
        $contentTable->dropColumn('list_format');
    }
}
