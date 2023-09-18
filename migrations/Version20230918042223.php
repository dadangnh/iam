<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230918042223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE kantor_luar (id UUID NOT NULL, jenis_kantor_id UUID NOT NULL, parent_id UUID DEFAULT NULL, pembina_id UUID DEFAULT NULL, nama VARCHAR(255) NOT NULL, level INT DEFAULT NULL, tanggal_aktif TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tanggal_nonaktif TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, sk VARCHAR(255) DEFAULT NULL, alamat TEXT DEFAULT NULL, telp VARCHAR(255) DEFAULT NULL, fax VARCHAR(255) DEFAULT NULL, zona_waktu VARCHAR(4) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, legacy_kode VARCHAR(10) DEFAULT NULL, legacy_kode_kpp VARCHAR(3) DEFAULT NULL, legacy_kode_kanwil VARCHAR(3) DEFAULT NULL, provinsi UUID DEFAULT NULL, kabupaten_kota UUID DEFAULT NULL, kecamatan UUID DEFAULT NULL, kelurahan UUID DEFAULT NULL, provinsi_name VARCHAR(255) DEFAULT NULL, kabupaten_kota_name VARCHAR(255) DEFAULT NULL, kecamatan_name VARCHAR(255) DEFAULT NULL, kelurahan_name VARCHAR(255) DEFAULT NULL, ministry_office_code VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FA9C6AA85EB4E08C ON kantor_luar (jenis_kantor_id)');
        $this->addSql('CREATE INDEX IDX_FA9C6AA8727ACA70 ON kantor_luar (parent_id)');
        $this->addSql('CREATE INDEX IDX_FA9C6AA84793DA59 ON kantor_luar (pembina_id)');
        $this->addSql('CREATE INDEX idx_kantor_luar_nama_status ON kantor_luar (id, nama, level, sk)');
        $this->addSql('CREATE INDEX idx_kantor_luar_legacy ON kantor_luar (id, legacy_kode, legacy_kode_kpp, legacy_kode_kanwil, ministry_office_code)');
        $this->addSql('CREATE INDEX idx_kantor_luar_relation ON kantor_luar (id, jenis_kantor_id, parent_id, level, pembina_id)');
        $this->addSql('CREATE INDEX idx_kantor_luar_location ON kantor_luar (id, latitude, longitude)');
        $this->addSql('CREATE INDEX idx_kantor_luar_active ON kantor_luar (id, nama, legacy_kode, tanggal_aktif, tanggal_nonaktif)');
        $this->addSql('CREATE INDEX idx_kantor_luar_position ON kantor_luar (id, nama, legacy_kode, provinsi, provinsi_name, kabupaten_kota, kabupaten_kota_name, kecamatan, kecamatan_name, kelurahan, kelurahan_name)');
        $this->addSql('COMMENT ON COLUMN kantor_luar.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.jenis_kantor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.pembina_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.tanggal_aktif IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.tanggal_nonaktif IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.provinsi IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.kabupaten_kota IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.kecamatan IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.kelurahan IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE kantor_luar ADD CONSTRAINT FK_FA9C6AA85EB4E08C FOREIGN KEY (jenis_kantor_id) REFERENCES jenis_kantor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE kantor_luar ADD CONSTRAINT FK_FA9C6AA8727ACA70 FOREIGN KEY (parent_id) REFERENCES kantor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE kantor_luar ADD CONSTRAINT FK_FA9C6AA84793DA59 FOREIGN KEY (pembina_id) REFERENCES kantor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE kantor_luar DROP CONSTRAINT FK_FA9C6AA85EB4E08C');
        $this->addSql('ALTER TABLE kantor_luar DROP CONSTRAINT FK_FA9C6AA8727ACA70');
        $this->addSql('ALTER TABLE kantor_luar DROP CONSTRAINT FK_FA9C6AA84793DA59');
        $this->addSql('DROP TABLE kantor_luar');
    }
}
