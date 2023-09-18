<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230915082228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE jenis_kantor_luar (id UUID NOT NULL, nama VARCHAR(255) NOT NULL, tipe VARCHAR(255) NOT NULL, klasifikasi INT NOT NULL, tanggal_aktif TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tanggal_nonaktif TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, legacy_id INT DEFAULT NULL, legacy_kode INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_jenis_kantor_luar_nama_status ON jenis_kantor_luar (id, nama, tipe, klasifikasi)');
        $this->addSql('CREATE INDEX idx_jenis_kantor_luar_legacy ON jenis_kantor_luar (id, legacy_kode, legacy_id)');
        $this->addSql('CREATE INDEX idx_jenis_kantor_luar_active ON jenis_kantor_luar (id, nama, legacy_kode, tanggal_aktif, tanggal_nonaktif)');
        $this->addSql('COMMENT ON COLUMN jenis_kantor_luar.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jenis_kantor_luar.tanggal_aktif IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN jenis_kantor_luar.tanggal_nonaktif IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE jenis_kantor_luar');
    }
}
