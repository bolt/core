<?php

declare(strict_types=1);

namespace Bolt\DoctrineMigrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaConfig;
use Doctrine\Migrations\AbstractMigration;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Version20201210105836 extends AbstractMigration
{
    /** @var string */
    private $tablePrefix;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        parent::__construct($connection, $logger);

        $this->tablePrefix = 'bolt';
    }

    public function getDescription(): string
    {
        return 'Bolt 4.2 Migration: bolt_reset_password and bolt_column.avatar';
    }

    public function up(Schema $schema): void
    {
        $schemaConfig = new SchemaConfig();
        $options = [
            'charset' => 'utf8',
        ];
        if ($this->connection->getDatabasePlatform()->getName() === 'mysql') {
            $options = [
                'engine' => 'InnoDB',
                'charset' => 'utf8mb4',
                'collate' => 'utf8mb4_unicode_ci'
            ];
        }
        $schemaConfig->setDefaultTableOptions($options);

        // Create the user avatar. See https://github.com/bolt/core/pull/2114
        $userTable = $schema->getTable($this->tablePrefix . '_user');

        if (!$userTable->hasColumn('avatar')) {
            $userTable->addColumn('avatar', 'string', ['notnull' => false, 'length' => 250]);
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
            $table->addForeignKeyConstraint(
                $this->tablePrefix . '_user',
                ['user_id'],
                ['id'],
                [],
                'FK_D04070DCA76ED395'
            );
        }

        $fieldTranstationTable = $schema->getTable($this->tablePrefix . '_field_translation');
        foreach ($fieldTranstationTable->getIndexes() as $index) {
            if ($index->getName() === 'bolt_field_translation_unique_translation') {
                $fieldTranstationTable->renameIndex('bolt_field_translation_unique_translation',
                    'field_translation_unique_translation');
            }
        }
    }

    public function down(Schema $schema): void
    {
    }
}
