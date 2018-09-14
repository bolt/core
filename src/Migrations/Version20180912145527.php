<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180912145527 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE content (id INT AUTO_INCREMENT NOT NULL, contenttype VARCHAR(191) NOT NULL, author_id INT NOT NULL, status VARCHAR(191) NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, published_at DATETIME DEFAULT NULL, depublished_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field (id INT AUTO_INCREMENT NOT NULL, content_id INT NOT NULL, name VARCHAR(191) NOT NULL, type VARCHAR(191) NOT NULL, value LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', parent_id INT NOT NULL, sortorder INT NOT NULL, locale VARCHAR(191) DEFAULT NULL, version INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE content');
        $this->addSql('DROP TABLE field');
    }
}
