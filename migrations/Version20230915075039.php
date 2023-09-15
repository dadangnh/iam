<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230915075039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_jabatan_luar (id UUID NOT NULL, nama VARCHAR(255) NOT NULL, legacy_kode VARCHAR(4) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_group_jabatan_luar_nama ON group_jabatan_luar (id, nama)');
        $this->addSql('CREATE INDEX idx_group_jabatan_luar_legacy ON group_jabatan_luar (id, legacy_kode)');
        $this->addSql('COMMENT ON COLUMN group_jabatan_luar.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE group_jabatan_luar');
    }
}
