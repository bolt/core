<?php

declare(strict_types=1);

namespace Bolt\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191220144350 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX content_field');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_field AS SELECT id, name, value, sortorder, locale, version, content_id, parent_id, type FROM bolt_field');
        $this->addSql('DROP TABLE bolt_field');
        $this->addSql('CREATE TABLE bolt_field (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(191) NOT NULL COLLATE BINARY, value CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , sortorder INTEGER NOT NULL, version INTEGER DEFAULT NULL, content_id INTEGER NOT NULL, parent_id INTEGER DEFAULT NULL, type VARCHAR(191) NOT NULL COLLATE BINARY, locale VARCHAR(2) DEFAULT NULL)');
        $this->addSql('INSERT INTO bolt_field (id, name, value, sortorder, locale, version, content_id, parent_id, type) SELECT id, name, value, sortorder, locale, version, content_id, parent_id, type FROM __temp__bolt_field');
        $this->addSql('DROP TABLE __temp__bolt_field');
        $this->addSql('CREATE UNIQUE INDEX content_field ON bolt_field (content_id, name)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX content_field');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_field AS SELECT id, name, value, sortorder, locale, version, content_id, parent_id, type FROM bolt_field');
        $this->addSql('DROP TABLE bolt_field');
        $this->addSql('CREATE TABLE bolt_field (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(191) NOT NULL, value CLOB NOT NULL --(DC2Type:json)
        , sortorder INTEGER NOT NULL, version INTEGER DEFAULT NULL, content_id INTEGER NOT NULL, parent_id INTEGER DEFAULT NULL, type VARCHAR(191) NOT NULL, locale VARCHAR(255) DEFAULT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO bolt_field (id, name, value, sortorder, locale, version, content_id, parent_id, type) SELECT id, name, value, sortorder, locale, version, content_id, parent_id, type FROM __temp__bolt_field');
        $this->addSql('DROP TABLE __temp__bolt_field');
        $this->addSql('CREATE UNIQUE INDEX content_field ON bolt_field (content_id, name)');
    }
}
