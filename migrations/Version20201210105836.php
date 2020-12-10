<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201210105836 extends AbstractMigration
{
    /** @var string */
    private $tablePrefix;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        parent::__construct($connection, $logger);

        $this->tablePrefix = 'bolt';
    }

    public function getDescription() : string
    {
        return 'Bolt 4.2 Migration: bolt_reset_password and bolt_column.avatar';
    }

    public function up(Schema $schema) : void
    {
        // Create the user avatar. See https://github.com/bolt/core/pull/2114
        $schema->getTable($this->tablePrefix . '_user')
            ->addColumn('avatar', 'string', ['notnull' => false, 'length' => 250]);

        // Create the reset password table. See https://github.com/bolt/core/pull/2131
        $resetPaswordTable = $schema->createTable($this->tablePrefix . '_password_request');
        $resetPaswordTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $resetPaswordTable->addColumn('user_id', 'integer', ['notnull' => true, '', 'default' => 0]);
        $resetPaswordTable->addForeignKeyConstraint($this->tablePrefix . '_user', ['user_id'], ['id'], ['onUpdate' => 'CASCADE']);
    }

    public function down(Schema $schema) : void
    {
    }
}
