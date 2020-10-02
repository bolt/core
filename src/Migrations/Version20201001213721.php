<?php

declare(strict_types=1);

namespace Bolt\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201001213721 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE bolt_reset_password_request (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , expires_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_D04070DCA76ED395 ON bolt_reset_password_request (user_id)');
        $this->addSql('DROP INDEX IDX_3431ED74A3934190');
        $this->addSql('DROP INDEX IDX_3431ED74AF0465EA');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_relation AS SELECT id, from_content_id, to_content_id, position FROM bolt_relation');
        $this->addSql('DROP TABLE bolt_relation');
        $this->addSql('CREATE TABLE bolt_relation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, from_content_id INTEGER NOT NULL, to_content_id INTEGER NOT NULL, position INTEGER NOT NULL, CONSTRAINT FK_3431ED74AF0465EA FOREIGN KEY (from_content_id) REFERENCES bolt_content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3431ED74A3934190 FOREIGN KEY (to_content_id) REFERENCES bolt_content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO bolt_relation (id, from_content_id, to_content_id, position) SELECT id, from_content_id, to_content_id, position FROM __temp__bolt_relation');
        $this->addSql('DROP TABLE __temp__bolt_relation');
        $this->addSql('CREATE INDEX IDX_3431ED74A3934190 ON bolt_relation (to_content_id)');
        $this->addSql('CREATE INDEX IDX_3431ED74AF0465EA ON bolt_relation (from_content_id)');
        $this->addSql('DROP INDEX IDX_C5BCC03C84A0A3ED');
        $this->addSql('DROP INDEX IDX_C5BCC03C9557E6F6');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_taxonomy_content AS SELECT taxonomy_id, content_id FROM bolt_taxonomy_content');
        $this->addSql('DROP TABLE bolt_taxonomy_content');
        $this->addSql('CREATE TABLE bolt_taxonomy_content (taxonomy_id INTEGER NOT NULL, content_id INTEGER NOT NULL, PRIMARY KEY(taxonomy_id, content_id), CONSTRAINT FK_C5BCC03C9557E6F6 FOREIGN KEY (taxonomy_id) REFERENCES bolt_taxonomy (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C5BCC03C84A0A3ED FOREIGN KEY (content_id) REFERENCES bolt_content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO bolt_taxonomy_content (taxonomy_id, content_id) SELECT taxonomy_id, content_id FROM __temp__bolt_taxonomy_content');
        $this->addSql('DROP TABLE __temp__bolt_taxonomy_content');
        $this->addSql('CREATE INDEX IDX_C5BCC03C84A0A3ED ON bolt_taxonomy_content (content_id)');
        $this->addSql('CREATE INDEX IDX_C5BCC03C9557E6F6 ON bolt_taxonomy_content (taxonomy_id)');
        $this->addSql('DROP INDEX status_idx');
        $this->addSql('DROP INDEX content_type_idx');
        $this->addSql('DROP INDEX IDX_F5AB2E9CF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_content AS SELECT id, author_id, content_type, status, created_at, modified_at, published_at, depublished_at FROM bolt_content');
        $this->addSql('DROP TABLE bolt_content');
        $this->addSql('CREATE TABLE bolt_content (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER DEFAULT NULL, content_type VARCHAR(191) NOT NULL COLLATE BINARY, status VARCHAR(191) NOT NULL COLLATE BINARY, created_at DATETIME NOT NULL, modified_at DATETIME DEFAULT NULL, published_at DATETIME DEFAULT NULL, depublished_at DATETIME DEFAULT NULL, CONSTRAINT FK_F5AB2E9CF675F31B FOREIGN KEY (author_id) REFERENCES bolt_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO bolt_content (id, author_id, content_type, status, created_at, modified_at, published_at, depublished_at) SELECT id, author_id, content_type, status, created_at, modified_at, published_at, depublished_at FROM __temp__bolt_content');
        $this->addSql('DROP TABLE __temp__bolt_content');
        $this->addSql('CREATE INDEX status_idx ON bolt_content (status)');
        $this->addSql('CREATE INDEX content_type_idx ON bolt_content (content_type)');
        $this->addSql('CREATE INDEX IDX_F5AB2E9CF675F31B ON bolt_content (author_id)');
        $this->addSql('DROP INDEX IDX_4A2EBBE5727ACA70');
        $this->addSql('DROP INDEX IDX_4A2EBBE584A0A3ED');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_field AS SELECT id, content_id, parent_id, name, sortorder, version, type FROM bolt_field');
        $this->addSql('DROP TABLE bolt_field');
        $this->addSql('CREATE TABLE bolt_field (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, content_id INTEGER NOT NULL, parent_id INTEGER DEFAULT NULL, name VARCHAR(191) NOT NULL COLLATE BINARY, sortorder INTEGER NOT NULL, version INTEGER DEFAULT NULL, type VARCHAR(191) NOT NULL COLLATE BINARY, CONSTRAINT FK_4A2EBBE584A0A3ED FOREIGN KEY (content_id) REFERENCES bolt_content (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4A2EBBE5727ACA70 FOREIGN KEY (parent_id) REFERENCES bolt_field (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO bolt_field (id, content_id, parent_id, name, sortorder, version, type) SELECT id, content_id, parent_id, name, sortorder, version, type FROM __temp__bolt_field');
        $this->addSql('DROP TABLE __temp__bolt_field');
        $this->addSql('CREATE INDEX IDX_4A2EBBE5727ACA70 ON bolt_field (parent_id)');
        $this->addSql('CREATE INDEX IDX_4A2EBBE584A0A3ED ON bolt_field (content_id)');
        $this->addSql('DROP INDEX field_translation_unique_translation');
        $this->addSql('DROP INDEX IDX_5C60C0542C2AC5D3');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_field_translation AS SELECT id, translatable_id, value, locale FROM bolt_field_translation');
        $this->addSql('DROP TABLE bolt_field_translation');
        $this->addSql('CREATE TABLE bolt_field_translation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, translatable_id INTEGER DEFAULT NULL, value CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , locale VARCHAR(5) NOT NULL COLLATE BINARY, CONSTRAINT FK_5C60C0542C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES bolt_field (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO bolt_field_translation (id, translatable_id, value, locale) SELECT id, translatable_id, value, locale FROM __temp__bolt_field_translation');
        $this->addSql('DROP TABLE __temp__bolt_field_translation');
        $this->addSql('CREATE UNIQUE INDEX field_translation_unique_translation ON bolt_field_translation (translatable_id, locale)');
        $this->addSql('CREATE INDEX IDX_5C60C0542C2AC5D3 ON bolt_field_translation (translatable_id)');
        $this->addSql('DROP INDEX UNIQ_8B90D313A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_user_auth_token AS SELECT id, user_id, useragent, validity FROM bolt_user_auth_token');
        $this->addSql('DROP TABLE bolt_user_auth_token');
        $this->addSql('CREATE TABLE bolt_user_auth_token (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, useragent VARCHAR(255) NOT NULL COLLATE BINARY, validity DATETIME NOT NULL, CONSTRAINT FK_8B90D313A76ED395 FOREIGN KEY (user_id) REFERENCES bolt_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO bolt_user_auth_token (id, user_id, useragent, validity) SELECT id, user_id, useragent, validity FROM __temp__bolt_user_auth_token');
        $this->addSql('DROP TABLE __temp__bolt_user_auth_token');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8B90D313A76ED395 ON bolt_user_auth_token (user_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_log AS SELECT id, message, context, level, level_name, created_at, extra, user, content, location FROM bolt_log');
        $this->addSql('DROP TABLE bolt_log');
        $this->addSql('CREATE TABLE bolt_log (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, message CLOB NOT NULL COLLATE BINARY, level SMALLINT NOT NULL, level_name VARCHAR(50) NOT NULL COLLATE BINARY, created_at DATETIME NOT NULL, content INTEGER DEFAULT NULL, context CLOB DEFAULT NULL --(DC2Type:array)
        , extra CLOB DEFAULT NULL --(DC2Type:array)
        , user CLOB DEFAULT NULL --(DC2Type:array)
        , location CLOB DEFAULT NULL --(DC2Type:array)
        )');
        $this->addSql('INSERT INTO bolt_log (id, message, context, level, level_name, created_at, extra, user, content, location) SELECT id, message, context, level, level_name, created_at, extra, user, content, location FROM __temp__bolt_log');
        $this->addSql('DROP TABLE __temp__bolt_log');
        $this->addSql('DROP INDEX IDX_7BF75FB1F675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_media AS SELECT id, author_id, location, path, filename, type, width, height, filesize, crop_x, crop_y, crop_zoom, created_at, modified_at, title, description, original_filename, copyright FROM bolt_media');
        $this->addSql('DROP TABLE bolt_media');
        $this->addSql('CREATE TABLE bolt_media (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER DEFAULT NULL, location VARCHAR(191) NOT NULL COLLATE BINARY, path CLOB NOT NULL COLLATE BINARY, filename VARCHAR(191) NOT NULL COLLATE BINARY, type VARCHAR(191) NOT NULL COLLATE BINARY, width INTEGER DEFAULT NULL, height INTEGER DEFAULT NULL, filesize INTEGER DEFAULT NULL, crop_x INTEGER DEFAULT NULL, crop_y INTEGER DEFAULT NULL, crop_zoom DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, title VARCHAR(191) DEFAULT NULL COLLATE BINARY, description VARCHAR(1000) DEFAULT NULL COLLATE BINARY, original_filename VARCHAR(1000) DEFAULT NULL COLLATE BINARY, copyright VARCHAR(191) DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_7BF75FB1F675F31B FOREIGN KEY (author_id) REFERENCES bolt_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO bolt_media (id, author_id, location, path, filename, type, width, height, filesize, crop_x, crop_y, crop_zoom, created_at, modified_at, title, description, original_filename, copyright) SELECT id, author_id, location, path, filename, type, width, height, filesize, crop_x, crop_y, crop_zoom, created_at, modified_at, title, description, original_filename, copyright FROM __temp__bolt_media');
        $this->addSql('DROP TABLE __temp__bolt_media');
        $this->addSql('CREATE INDEX IDX_7BF75FB1F675F31B ON bolt_media (author_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE bolt_reset_password_request');
        $this->addSql('DROP INDEX IDX_F5AB2E9CF675F31B');
        $this->addSql('DROP INDEX content_type_idx');
        $this->addSql('DROP INDEX status_idx');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_content AS SELECT id, author_id, content_type, status, created_at, modified_at, published_at, depublished_at FROM bolt_content');
        $this->addSql('DROP TABLE bolt_content');
        $this->addSql('CREATE TABLE bolt_content (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER DEFAULT NULL, content_type VARCHAR(191) NOT NULL, status VARCHAR(191) NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME DEFAULT NULL, published_at DATETIME DEFAULT NULL, depublished_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO bolt_content (id, author_id, content_type, status, created_at, modified_at, published_at, depublished_at) SELECT id, author_id, content_type, status, created_at, modified_at, published_at, depublished_at FROM __temp__bolt_content');
        $this->addSql('DROP TABLE __temp__bolt_content');
        $this->addSql('CREATE INDEX IDX_F5AB2E9CF675F31B ON bolt_content (author_id)');
        $this->addSql('CREATE INDEX content_type_idx ON bolt_content (content_type)');
        $this->addSql('CREATE INDEX status_idx ON bolt_content (status)');
        $this->addSql('DROP INDEX IDX_4A2EBBE584A0A3ED');
        $this->addSql('DROP INDEX IDX_4A2EBBE5727ACA70');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_field AS SELECT id, content_id, parent_id, name, sortorder, version, type FROM bolt_field');
        $this->addSql('DROP TABLE bolt_field');
        $this->addSql('CREATE TABLE bolt_field (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, content_id INTEGER NOT NULL, parent_id INTEGER DEFAULT NULL, name VARCHAR(191) NOT NULL, sortorder INTEGER NOT NULL, version INTEGER DEFAULT NULL, type VARCHAR(191) NOT NULL)');
        $this->addSql('INSERT INTO bolt_field (id, content_id, parent_id, name, sortorder, version, type) SELECT id, content_id, parent_id, name, sortorder, version, type FROM __temp__bolt_field');
        $this->addSql('DROP TABLE __temp__bolt_field');
        $this->addSql('CREATE INDEX IDX_4A2EBBE584A0A3ED ON bolt_field (content_id)');
        $this->addSql('CREATE INDEX IDX_4A2EBBE5727ACA70 ON bolt_field (parent_id)');
        $this->addSql('DROP INDEX IDX_5C60C0542C2AC5D3');
        $this->addSql('DROP INDEX field_translation_unique_translation');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_field_translation AS SELECT id, translatable_id, value, locale FROM bolt_field_translation');
        $this->addSql('DROP TABLE bolt_field_translation');
        $this->addSql('CREATE TABLE bolt_field_translation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, translatable_id INTEGER DEFAULT NULL, value CLOB NOT NULL --(DC2Type:json)
        , locale VARCHAR(5) NOT NULL)');
        $this->addSql('INSERT INTO bolt_field_translation (id, translatable_id, value, locale) SELECT id, translatable_id, value, locale FROM __temp__bolt_field_translation');
        $this->addSql('DROP TABLE __temp__bolt_field_translation');
        $this->addSql('CREATE INDEX IDX_5C60C0542C2AC5D3 ON bolt_field_translation (translatable_id)');
        $this->addSql('CREATE UNIQUE INDEX field_translation_unique_translation ON bolt_field_translation (translatable_id, locale)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_log AS SELECT id, message, context, level, level_name, created_at, extra, user, content, location FROM bolt_log');
        $this->addSql('DROP TABLE bolt_log');
        $this->addSql('CREATE TABLE bolt_log (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, message CLOB NOT NULL, level SMALLINT NOT NULL, level_name VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, content INTEGER DEFAULT NULL, context CLOB DEFAULT \'NULL --(DC2Type:array)\' COLLATE BINARY --(DC2Type:array)
        , extra CLOB DEFAULT \'NULL --(DC2Type:array)\' COLLATE BINARY --(DC2Type:array)
        , user CLOB DEFAULT \'NULL --(DC2Type:array)\' COLLATE BINARY --(DC2Type:array)
        , location CLOB DEFAULT \'NULL --(DC2Type:array)\' COLLATE BINARY --(DC2Type:array)
        )');
        $this->addSql('INSERT INTO bolt_log (id, message, context, level, level_name, created_at, extra, user, content, location) SELECT id, message, context, level, level_name, created_at, extra, user, content, location FROM __temp__bolt_log');
        $this->addSql('DROP TABLE __temp__bolt_log');
        $this->addSql('DROP INDEX IDX_7BF75FB1F675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_media AS SELECT id, author_id, location, path, filename, type, width, height, filesize, crop_x, crop_y, crop_zoom, created_at, modified_at, title, description, original_filename, copyright FROM bolt_media');
        $this->addSql('DROP TABLE bolt_media');
        $this->addSql('CREATE TABLE bolt_media (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER DEFAULT NULL, location VARCHAR(191) NOT NULL, path CLOB NOT NULL, filename VARCHAR(191) NOT NULL, type VARCHAR(191) NOT NULL, width INTEGER DEFAULT NULL, height INTEGER DEFAULT NULL, filesize INTEGER DEFAULT NULL, crop_x INTEGER DEFAULT NULL, crop_y INTEGER DEFAULT NULL, crop_zoom DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, title VARCHAR(191) DEFAULT NULL, description VARCHAR(1000) DEFAULT NULL, original_filename VARCHAR(1000) DEFAULT NULL, copyright VARCHAR(191) DEFAULT NULL)');
        $this->addSql('INSERT INTO bolt_media (id, author_id, location, path, filename, type, width, height, filesize, crop_x, crop_y, crop_zoom, created_at, modified_at, title, description, original_filename, copyright) SELECT id, author_id, location, path, filename, type, width, height, filesize, crop_x, crop_y, crop_zoom, created_at, modified_at, title, description, original_filename, copyright FROM __temp__bolt_media');
        $this->addSql('DROP TABLE __temp__bolt_media');
        $this->addSql('CREATE INDEX IDX_7BF75FB1F675F31B ON bolt_media (author_id)');
        $this->addSql('DROP INDEX IDX_3431ED74AF0465EA');
        $this->addSql('DROP INDEX IDX_3431ED74A3934190');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_relation AS SELECT id, from_content_id, to_content_id, position FROM bolt_relation');
        $this->addSql('DROP TABLE bolt_relation');
        $this->addSql('CREATE TABLE bolt_relation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, from_content_id INTEGER NOT NULL, to_content_id INTEGER NOT NULL, position INTEGER NOT NULL)');
        $this->addSql('INSERT INTO bolt_relation (id, from_content_id, to_content_id, position) SELECT id, from_content_id, to_content_id, position FROM __temp__bolt_relation');
        $this->addSql('DROP TABLE __temp__bolt_relation');
        $this->addSql('CREATE INDEX IDX_3431ED74AF0465EA ON bolt_relation (from_content_id)');
        $this->addSql('CREATE INDEX IDX_3431ED74A3934190 ON bolt_relation (to_content_id)');
        $this->addSql('DROP INDEX IDX_C5BCC03C9557E6F6');
        $this->addSql('DROP INDEX IDX_C5BCC03C84A0A3ED');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_taxonomy_content AS SELECT taxonomy_id, content_id FROM bolt_taxonomy_content');
        $this->addSql('DROP TABLE bolt_taxonomy_content');
        $this->addSql('CREATE TABLE bolt_taxonomy_content (taxonomy_id INTEGER NOT NULL, content_id INTEGER NOT NULL, PRIMARY KEY(taxonomy_id, content_id))');
        $this->addSql('INSERT INTO bolt_taxonomy_content (taxonomy_id, content_id) SELECT taxonomy_id, content_id FROM __temp__bolt_taxonomy_content');
        $this->addSql('DROP TABLE __temp__bolt_taxonomy_content');
        $this->addSql('CREATE INDEX IDX_C5BCC03C9557E6F6 ON bolt_taxonomy_content (taxonomy_id)');
        $this->addSql('CREATE INDEX IDX_C5BCC03C84A0A3ED ON bolt_taxonomy_content (content_id)');
        $this->addSql('DROP INDEX UNIQ_8B90D313A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__bolt_user_auth_token AS SELECT id, user_id, useragent, validity FROM bolt_user_auth_token');
        $this->addSql('DROP TABLE bolt_user_auth_token');
        $this->addSql('CREATE TABLE bolt_user_auth_token (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, useragent VARCHAR(255) NOT NULL, validity DATETIME NOT NULL)');
        $this->addSql('INSERT INTO bolt_user_auth_token (id, user_id, useragent, validity) SELECT id, user_id, useragent, validity FROM __temp__bolt_user_auth_token');
        $this->addSql('DROP TABLE __temp__bolt_user_auth_token');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8B90D313A76ED395 ON bolt_user_auth_token (user_id)');
    }
}
