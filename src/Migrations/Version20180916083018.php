<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180916083018 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bolt_post (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, summary VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, published_at DATETIME NOT NULL, INDEX IDX_807F8D56F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bolt_post_tag (post_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_C7690CD4B89032C (post_id), INDEX IDX_C7690CDBAD26311 (tag_id), PRIMARY KEY(post_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bolt_tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(190) NOT NULL, UNIQUE INDEX UNIQ_96D0BCE85E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bolt_user (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, username VARCHAR(190) NOT NULL, email VARCHAR(190) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_57663792F85E0677 (username), UNIQUE INDEX UNIQ_57663792E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bolt_content (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, contenttype VARCHAR(191) NOT NULL, status VARCHAR(191) NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, published_at DATETIME DEFAULT NULL, depublished_at DATETIME NOT NULL, INDEX IDX_F5AB2E9CF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bolt_field (id INT AUTO_INCREMENT NOT NULL, content_id INT NOT NULL, name VARCHAR(191) NOT NULL, type VARCHAR(191) NOT NULL, value LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', parent_id INT DEFAULT NULL, sortorder INT NOT NULL, locale VARCHAR(191) DEFAULT NULL, version INT DEFAULT NULL, INDEX IDX_4A2EBBE584A0A3ED (content_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bolt_comment (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, author_id INT NOT NULL, content LONGTEXT NOT NULL, published_at DATETIME NOT NULL, INDEX IDX_9F1A4C594B89032C (post_id), INDEX IDX_9F1A4C59F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bolt_post ADD CONSTRAINT FK_807F8D56F675F31B FOREIGN KEY (author_id) REFERENCES bolt_user (id)');
        $this->addSql('ALTER TABLE bolt_post_tag ADD CONSTRAINT FK_C7690CD4B89032C FOREIGN KEY (post_id) REFERENCES bolt_post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bolt_post_tag ADD CONSTRAINT FK_C7690CDBAD26311 FOREIGN KEY (tag_id) REFERENCES bolt_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bolt_content ADD CONSTRAINT FK_F5AB2E9CF675F31B FOREIGN KEY (author_id) REFERENCES bolt_user (id)');
        $this->addSql('ALTER TABLE bolt_field ADD CONSTRAINT FK_4A2EBBE584A0A3ED FOREIGN KEY (content_id) REFERENCES bolt_content (id)');
        $this->addSql('ALTER TABLE bolt_comment ADD CONSTRAINT FK_9F1A4C594B89032C FOREIGN KEY (post_id) REFERENCES bolt_post (id)');
        $this->addSql('ALTER TABLE bolt_comment ADD CONSTRAINT FK_9F1A4C59F675F31B FOREIGN KEY (author_id) REFERENCES bolt_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bolt_post_tag DROP FOREIGN KEY FK_C7690CD4B89032C');
        $this->addSql('ALTER TABLE bolt_comment DROP FOREIGN KEY FK_9F1A4C594B89032C');
        $this->addSql('ALTER TABLE bolt_post_tag DROP FOREIGN KEY FK_C7690CDBAD26311');
        $this->addSql('ALTER TABLE bolt_post DROP FOREIGN KEY FK_807F8D56F675F31B');
        $this->addSql('ALTER TABLE bolt_content DROP FOREIGN KEY FK_F5AB2E9CF675F31B');
        $this->addSql('ALTER TABLE bolt_comment DROP FOREIGN KEY FK_9F1A4C59F675F31B');
        $this->addSql('ALTER TABLE bolt_field DROP FOREIGN KEY FK_4A2EBBE584A0A3ED');
        $this->addSql('DROP TABLE bolt_post');
        $this->addSql('DROP TABLE bolt_post_tag');
        $this->addSql('DROP TABLE bolt_tag');
        $this->addSql('DROP TABLE bolt_user');
        $this->addSql('DROP TABLE bolt_content');
        $this->addSql('DROP TABLE bolt_field');
        $this->addSql('DROP TABLE bolt_comment');
    }
}
