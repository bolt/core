<?php

declare(strict_types=1);

namespace Bolt\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190301121752 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
        ALTER TABLE bolt_field ADD field_type VARCHAR(255) NOT NULL, CHANGE parent_id parent_id INT DEFAULT NULL, CHANGE value value LONGTEXT NOT NULL, CHANGE version version INT DEFAULT NULL;
        ALTER TABLE bolt_user CHANGE lastseen_at lastseen_at DATETIME DEFAULT NULL, CHANGE last_ip last_ip VARCHAR(100) DEFAULT NULL, CHANGE locale locale VARCHAR(191) DEFAULT NULL, CHANGE backend_theme backend_theme VARCHAR(191) DEFAULT NULL;
        ALTER TABLE bolt_content CHANGE modified_at modified_at DATETIME DEFAULT NULL, CHANGE published_at published_at DATETIME DEFAULT NULL, CHANGE depublished_at depublished_at DATETIME DEFAULT NULL;
        ALTER TABLE bolt_media CHANGE width width INT DEFAULT NULL, CHANGE height height INT DEFAULT NULL, CHANGE title title VARCHAR(191) DEFAULT NULL, CHANGE description description VARCHAR(1000) DEFAULT NULL, CHANGE original_filename original_filename VARCHAR(1000) DEFAULT NULL, CHANGE copyright copyright VARCHAR(191) DEFAULT NULL;
        ');

        $this->addSql("
        UPDATE bolt_field f
        SET
            f.value = REPLACE(JSON_EXTRACT(f.value, '$[0]'), '\"', ''),
            f.field_type = 'string'
        WHERE f.value LIKE '[%';
        
        UPDATE bolt_field f
        SET
          f.field_type = 'array'
        WHERE f.field_type != 'string';
        ");
    }

    public function down(Schema $schema): void
    {
        // one way ticket
    }
}
