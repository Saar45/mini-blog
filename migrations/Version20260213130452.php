<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260213130452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        
        // Add columns as nullable first
        $this->addSql('ALTER TABLE post ADD is_published TINYINT DEFAULT 0 NOT NULL, ADD created_at DATETIME DEFAULT NULL');
        
        // Update existing posts: set created_at to published_at and mark as published
        $this->addSql('UPDATE post SET created_at = published_at, is_published = 1 WHERE created_at IS NULL');
        
        // Make created_at NOT NULL after updating existing rows
        $this->addSql('ALTER TABLE post CHANGE created_at created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP is_published, DROP created_at');
    }
}
