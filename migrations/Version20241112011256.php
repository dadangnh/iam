<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241112011256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_jabatan_atribut_nama');
        $this->addSql('ALTER TABLE jabatan_atribut ADD parent_atribut_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE jabatan_atribut RENAME COLUMN nama TO nama_atribut');
        $this->addSql('CREATE INDEX idx_jabatan_atribut_nama ON jabatan_atribut (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX idx_jabatan_atribut_nama');
        $this->addSql('ALTER TABLE jabatan_atribut DROP parent_atribut_id');
        $this->addSql('ALTER TABLE jabatan_atribut RENAME COLUMN nama_atribut TO nama');
        $this->addSql('CREATE INDEX idx_jabatan_atribut_nama ON jabatan_atribut (id, nama)');
    }
}
