<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260212155805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add slug field to posts and generate slugs for existing posts';
    }

    public function up(Schema $schema): void
    {
        // Add slug column as nullable first
        $this->addSql('ALTER TABLE post ADD slug VARCHAR(255) DEFAULT NULL');
        
        // Generate slugs for existing posts
        $this->addSql("
            UPDATE post 
            SET slug = LOWER(
                TRIM(
                    BOTH '-' FROM 
                    REGEXP_REPLACE(
                        REGEXP_REPLACE(title, '[^a-zA-Z0-9]+', '-'),
                        '-+', '-'
                    )
                )
            )
        ");
        
        // Now make slug NOT NULL and unique
        $this->addSql('ALTER TABLE post MODIFY slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8D989D9B62 ON post (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_5A8A6C8D989D9B62 ON post');
        $this->addSql('ALTER TABLE post DROP slug');
    }
}
