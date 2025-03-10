<?php

declare(strict_types=1);

namespace Bolt\DoctrineMigrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaConfig;
use Doctrine\DBAL\Schema\Table;
use Doctrine\Migrations\AbstractMigration;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Version20201210105836 extends AbstractMigration
{
    /** @var string */
    private $tablePrefix = 'bolt';

    public function getDescription(): string
    {
        return 'Bolt 4.2 Migration: bolt_reset_password and bolt_column.avatar';
    }

    public function up(Schema $schema): void
    {
        $schemaConfig = new SchemaConfig();
        $schemaConfig->setDefaultTableOptions([
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_unicode_ci'
        ]);

        //Check if table exist, if not create directly in Bolt 5.1 format
        if ($schema->hasTable($this->tablePrefix . '_content') === false) {

            $table = $schema->createTable($this->tablePrefix . '_content');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('author_id', 'integer', ['notnull' => false]);
            $table->addColumn('content_type', 'string', ['notnull' => true, 'length' => 191]);
            $table->addColumn('status', 'string', ['notnull' => true, 'length' => 191]);
            $table->addColumn('created_at', 'datetime', ['notnull' => true]);
            $table->addColumn('modified_at', 'datetime', ['notnull' => false]);
            $table->addColumn('published_at', 'datetime', ['notnull' => false]);
            $table->addColumn('depublished_at', 'datetime', ['notnull' => false]);
            $table->addIndex(['author_id'], 'IDX_F5AB2E9CF675F31B');
            $table->addIndex(['content_type'], 'content_type_idx');
            $table->addIndex(['status'], 'status_idx');
            $table->setPrimaryKey(['id']);
            $table->setSchemaConfig($schemaConfig);

        }
        if ($schema->hasTable($this->tablePrefix . '_field') === false) {

            $table = $schema->createTable($this->tablePrefix . '_field');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('content_id', 'integer', ['notnull' => true]);
            $table->addColumn('parent_id', 'integer', ['notnull' => false]);
            $table->addColumn('name', 'string', ['notnull' => true, 'length' => 191]);
            $table->addColumn('sortorder', 'integer', ['notnull' => true]);
            $table->addColumn('version', 'integer', ['notnull' => false]);
            $table->addColumn('type', 'string', ['notnull' => true, 'length' => 191]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['content_id'], 'IDX_4A2EBBE584A0A3ED');
            $table->addIndex(['parent_id'], 'IDX_4A2EBBE5727ACA70');
            $table->setSchemaConfig($schemaConfig);
        }
        if ($schema->hasTable($this->tablePrefix . '_field_translation') === false) {

            $table = $schema->createTable($this->tablePrefix . '_field_translation');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('translatable_id', 'integer', ['notnull' => false]);
            $table->addColumn('value', 'json', ['notnull' => true]);
            $table->addColumn('locale', 'string', ['notnull' => true, 'length' => 5]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['translatable_id'], 'IDX_5C60C0542C2AC5D3');
            $table->addUniqueIndex(['translatable_id', 'locale'], 'field_translation_unique_translation');
            $table->setSchemaConfig($schemaConfig);
        }
        if ($schema->hasTable($this->tablePrefix . '_log') === false) {

            $table = $schema->createTable($this->tablePrefix . '_log');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('message', 'text', ['notnull' => true]);
            $table->addColumn('context', 'text', ['notnull' => false, 'comment' => '(DC2Type:array)']);
            $table->addColumn('level', 'smallint', ['notnull' => true]);
            $table->addColumn('level_name', 'string', ['notnull' => true, 'length' => 50]);
            $table->addColumn('created_at', 'datetime', ['notnull' => true]);
            $table->addColumn('extra', 'text', ['notnull' => false, 'comment' => '(DC2Type:array)']);
            $table->addColumn('`user`', 'text', ['notnull' => false, 'comment' => '(DC2Type:array)']);
            $table->addColumn('content', 'integer', ['notnull' => false]);
            $table->addColumn('location', 'text', ['notnull' => false, 'comment' => '(DC2Type:array)']);

            $table->setPrimaryKey(['id']);
            $table->setSchemaConfig($schemaConfig);
        }
        if ($schema->hasTable($this->tablePrefix . '_media') === false) {

            $table = $schema->createTable($this->tablePrefix . '_media');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('author_id', 'integer', ['notnull' => false]);
            $table->addColumn('location', 'string', ['notnull' => true, 'length' => 191]);
            $table->addColumn('path', 'text', ['notnull' => true]);
            $table->addColumn('filename', 'string', ['notnull' => true, 'length' => 191]);
            $table->addColumn('type', 'string', ['notnull' => true, 'length' => 191]);
            $table->addColumn('width', 'integer', ['notnull' => false]);
            $table->addColumn('height', 'integer', ['notnull' => false]);
            $table->addColumn('filesize', 'integer', ['notnull' => false]);
            $table->addColumn('crop_x', 'integer', ['notnull' => false]);
            $table->addColumn('crop_y', 'integer', ['notnull' => false]);
            $table->addColumn('crop_zoom', 'float', ['notnull' => false]);
            $table->addColumn('created_at', 'datetime', ['notnull' => true]);
            $table->addColumn('modified_at', 'datetime', ['notnull' => true]);
            $table->addColumn('title', 'string', ['notnull' => false, 'length' => 191]);
            $table->addColumn('description', 'string', ['notnull' => false, 'length' => 1000]);
            $table->addColumn('original_filename', 'string', ['notnull' => false, 'length' => 1000]);
            $table->addColumn('copyright', 'string', ['notnull' => false, 'length' => 191]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['author_id'], 'IDX_7BF75FB1F675F31B');
            $table->setSchemaConfig($schemaConfig);
        }
        if ($schema->hasTable($this->tablePrefix . '_relation') === false) {

            $table = $schema->createTable($this->tablePrefix . '_relation');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('from_content_id', 'integer', ['notnull' => true]);
            $table->addColumn('to_content_id', 'integer', ['notnull' => true]);
            $table->addColumn('position', 'integer', ['notnull' => true]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['from_content_id'], 'IDX_3431ED74AF0465EA');
            $table->addIndex(['to_content_id'], 'IDX_3431ED74A3934190');
            $table->setSchemaConfig($schemaConfig);
        }

        // Create the reset password table. See https://github.com/bolt/core/pull/2131
        if ($schema->hasTable($this->tablePrefix . '_reset_password_request') === false) {

            $table = $schema->createTable($this->tablePrefix . '_reset_password_request');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('user_id', 'integer', ['notnull' => true]);
            $table->addColumn('selector', 'string', ['notnull' => true, 'length' => 20]);
            $table->addColumn('hashed_token', 'string', ['notnull' => true, 'length' => 100]);
            $table->addColumn('requested_at', 'datetime',
                ['notnull' => true, 'comment' => '(DC2Type:datetime_immutable)']);
            $table->addColumn('expires_at', 'datetime',
                ['notnull' => true, 'comment' => '(DC2Type:datetime_immutable)']);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'IDX_D04070DCA76ED395');
            $table->setSchemaConfig($schemaConfig);
        }
        if ($schema->hasTable($this->tablePrefix . '_taxonomy') === false) {

            $table = $schema->createTable($this->tablePrefix . '_taxonomy');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('type', 'string', ['notnull' => true, 'length' => 191]);
            $table->addColumn('slug', 'string', ['notnull' => true, 'length' => 191]);
            $table->addColumn('name', 'string', ['notnull' => true, 'length' => 191]);
            $table->addColumn('sortorder', 'integer', ['notnull' => true]);

            $table->setPrimaryKey(['id']);
            $table->setSchemaConfig($schemaConfig);
        }
        if ($schema->hasTable($this->tablePrefix . '_taxonomy_content') === false) {

            $table = $schema->createTable($this->tablePrefix . '_taxonomy_content');
            $table->addColumn('taxonomy_id', 'integer', ['notnull' => true]);
            $table->addColumn('content_id', 'integer', ['notnull' => true]);

            $table->setPrimaryKey(['taxonomy_id', 'content_id']);
            $table->addIndex(['taxonomy_id'], 'IDX_C5BCC03C9557E6F6');
            $table->addIndex(['content_id'], 'IDX_C5BCC03C84A0A3ED');
            $table->setSchemaConfig($schemaConfig);
        }
        if ($schema->hasTable($this->tablePrefix . '_user') === false) {

            $table = $schema->createTable($this->tablePrefix . '_user');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('display_name', 'string', ['length' => 255, 'notnull' => true]);
            $table->addColumn('username', 'string', ['length' => 191, 'notnull' => true]);
            $table->addColumn('email', 'string', ['length' => 191, 'notnull' => true]);
            $table->addColumn('password', 'string', ['length' => 191, 'notnull' => true]);
            $table->addColumn('roles', 'json', ['notnull' => true]);
            $table->addColumn('lastseen_at', 'datetime', ['notnull' => false]);
            $table->addColumn('last_ip', 'string', ['length' => 100, 'notnull' => false]);
            $table->addColumn('locale', 'string', ['length' => 191, 'notnull' => false]);
            $table->addColumn('backend_theme', 'string', ['length' => 191, 'notnull' => false]);
            $table->addColumn('status', 'string', ['length' => 30, 'notnull' => true, 'default' => 'enabled']);
            $table->addColumn('avatar', 'string', ['length' => 250, 'notnull' => false]);

            $table->setPrimaryKey(['id']);
            $table->addUniqueIndex(['username'], 'UNIQ_57663792F85E0677');
            $table->addUniqueIndex(['email'], 'UNIQ_57663792E7927C74');
            $schemaConfig = new SchemaConfig();
            $schemaConfig->setDefaultTableOptions([
                'collation' => 'utf8mb4_unicode_ci',
                'charset' => 'utf8mb4',
                'engine' => 'InnoDB'
            ]);
            $table->setSchemaConfig($schemaConfig);

        }
        if ($schema->hasTable($this->tablePrefix . '_user_auth_token') === false) {

            $table = $schema->createTable($this->tablePrefix . '_user_auth_token');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('user_id', 'integer', ['notnull' => false]);
            $table->addColumn('useragent', 'string', ['notnull' => true, 'length' => 255]);
            $table->addColumn('validity', 'datetime', ['notnull' => true]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'IDX_8B90D313A76ED395');
            $table->setSchemaConfig($schemaConfig);
        }
//
        if ($schema->hasTable($this->tablePrefix . '_content') && $schema->hasTable($this->tablePrefix . '_user')) {
            $this->executeQueryIfForeignKeyWrong(
                $schema->getTable($this->tablePrefix . '_content'),
                ['author_id'],
                '_user',
                ['id'],
                'FK_F5AB2E9CF675F31B'
            );
        }
        if ($schema->hasTable($this->tablePrefix . '_field') && $schema->hasTable($this->tablePrefix . '_content')) {
            $this->executeQueryIfForeignKeyWrong(
                $schema->getTable($this->tablePrefix . '_field'),
                ['content_id'],
                '_content',
                ['id'],
                'FK_4A2EBBE584A0A3ED'
            );
        }
        if ($schema->hasTable($this->tablePrefix . '_field')) {
            $this->executeQueryIfForeignKeyWrong(
                $schema->getTable($this->tablePrefix . '_field'),
                ['parent_id'],
                '_field',
                ['id'],
                'FK_4A2EBBE5727ACA70',
                ['onDelete' => 'CASCADE']
            );
        }
        if ($schema->hasTable($this->tablePrefix . '_field_translation') && $schema->hasTable($this->tablePrefix . '_field')) {
            $this->executeQueryIfForeignKeyWrong(
                $schema->getTable($this->tablePrefix . '_field_translation'),
                ['translatable_id'],
                '_field',
                ['id'],
                'FK_5C60C0542C2AC5D3',
                ['onDelete' => 'CASCADE']
            );
        }

        if ($schema->hasTable($this->tablePrefix . '_media') && $schema->hasTable($this->tablePrefix . '_user')) {
            $this->executeQueryIfForeignKeyWrong(
                $schema->getTable($this->tablePrefix . '_media'),
                ['author_id'],
                '_user',
                ['id'],
                'FK_7BF75FB1F675F31B'
            );
        }

        if ($schema->hasTable($this->tablePrefix . '_relation') && $schema->hasTable($this->tablePrefix . '_content')) {
            $this->executeQueryIfForeignKeyWrong(
                $schema->getTable($this->tablePrefix . '_relation'),
                ['from_content_id'],
                '_content',
                ['id'],
                'FK_3431ED74AF0465EA',
                ['onDelete' => 'CASCADE']
            );
        }

        if ($schema->hasTable($this->tablePrefix . '_relation') && $schema->hasTable($this->tablePrefix . '_content')) {
            $this->executeQueryIfForeignKeyWrong(
                $schema->getTable($this->tablePrefix . '_relation'),
                ['to_content_id'],
                '_content',
                ['id'],
                'FK_3431ED74A3934190',
                ['onDelete' => 'CASCADE']
            );
        }

        if ($schema->hasTable($this->tablePrefix . '_reset_password_request') && $schema->hasTable($this->tablePrefix . '_user')) {
            $this->executeQueryIfForeignKeyWrong(
                $schema->getTable($this->tablePrefix . '_reset_password_request'),
                ['user_id'],
                '_user',
                ['id'],
                'FK_D04070DCA76ED395'
            );
        }

        if ($schema->hasTable($this->tablePrefix . '_taxonomy_content') && $schema->hasTable($this->tablePrefix . '_taxonomy')) {
            $this->executeQueryIfForeignKeyWrong(
                $schema->getTable($this->tablePrefix . '_taxonomy_content'),
                ['taxonomy_id'],
                '_taxonomy',
                ['id'],
                'FK_C5BCC03C9557E6F6',
                ['onDelete' => 'CASCADE']
            );
        }

        if ($schema->hasTable($this->tablePrefix . '_taxonomy_content') && $schema->hasTable($this->tablePrefix . '_content')) {
            $this->executeQueryIfForeignKeyWrong(
                $schema->getTable($this->tablePrefix . '_taxonomy_content'),
                ['content_id'],
                '_content',
                ['id'],
                'FK_C5BCC03C84A0A3ED',
                ['onDelete' => 'CASCADE']
            );
        }

        if ($schema->hasTable($this->tablePrefix . '_user_auth_token') && $schema->hasTable($this->tablePrefix . '_user')) {
            $this->executeQueryIfForeignKeyWrong(
                $schema->getTable($this->tablePrefix . '_user_auth_token'),
                ['user_id'],
                '_user',
                ['id'],
                'FK_8B90D313A76ED395'
            );
        }

        $fieldTranstationTable = $schema->getTable($this->tablePrefix . '_field_translation');
        foreach ($fieldTranstationTable->getIndexes() as $index) {
            if ($index->getName() === 'bolt_field_translation_unique_translation') {
                $fieldTranstationTable->renameIndex('bolt_field_translation_unique_translation',
                    'field_translation_unique_translation');
            }
        }


        // Create the user avatar. See https://github.com/bolt/core/pull/2114
        $userTable = $schema->getTable($this->tablePrefix . '_user');

        if (!$userTable->hasColumn('avatar')) {
            $userTable->addColumn('avatar', 'string', ['notnull' => false, 'length' => 250]);
        }


    }

    public function down(Schema $schema): void
    {
        // No down necessary
    }

    private function executeQueryIfForeignKeyWrong(
        Table $table,
        array $fieldNames,
        string $targetTableName,
        array $targetFieldNames,
        string $fkName,
        array $options = []
    ) {
        foreach ($table->getForeignKeys() as $fk) {
            //compare source fields list and target fields list
            $fieldDiff = array_diff($fieldNames, $fk->getLocalColumns());
            $fieldTargetDiff = array_diff($targetFieldNames, $fk->getForeignColumns());
            $fieldDiffInverted = array_diff($fk->getLocalColumns(), $fieldNames);
            $fieldTargetDiffInverted = array_diff($fk->getForeignColumns(), $targetFieldNames);

            if (
                $fk->getForeignTableName() === $this->tablePrefix . $targetTableName
                && \count($fieldDiff) === 0
                && \count($fieldTargetDiff) === 0
                && \count($fieldDiffInverted) === 0
                && \count($fieldTargetDiffInverted) === 0
            ) {
                //foreign key exist but have a wrong name, remove it before add execute query.
                if ($fk->getName() !== $fkName) {
                    $table->removeForeignKey($fk->getName());
                    break;
                }
                //do not execute query, the foreign key is correct
                return;
            }
        }

        $table->addForeignKeyConstraint(
            $this->tablePrefix . $targetTableName,
            $fieldNames,
            $targetFieldNames,
            $options,
            $fkName
        );

    }
}
