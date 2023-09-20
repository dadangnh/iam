<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230919034725 extends AbstractMigration
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
        $this->addSql('CREATE TABLE jabatan_luar (id UUID NOT NULL, eselon_id UUID DEFAULT NULL, group_jabatan_luar_id UUID DEFAULT NULL, nama VARCHAR(255) NOT NULL, level INT NOT NULL, jenis VARCHAR(255) NOT NULL, tanggal_aktif TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tanggal_nonaktif TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, sk VARCHAR(255) DEFAULT NULL, legacy_kode VARCHAR(4) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DAA10CA92D0196DB ON jabatan_luar (eselon_id)');
        $this->addSql('CREATE INDEX IDX_DAA10CA931FE2AE ON jabatan_luar (group_jabatan_luar_id)');
        $this->addSql('CREATE INDEX idx_jabatan_luar_nama_status ON jabatan_luar (id, nama, level, jenis)');
        $this->addSql('CREATE INDEX idx_jabatan_luar_legacy ON jabatan_luar (id, legacy_kode)');
        $this->addSql('CREATE INDEX idx_jabatan_luar_relation ON jabatan_luar (id, eselon_id)');
        $this->addSql('CREATE INDEX idx_jabatan_luar_active ON jabatan_luar (id, nama, legacy_kode, tanggal_aktif, tanggal_nonaktif)');
        $this->addSql('COMMENT ON COLUMN jabatan_luar.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_luar.eselon_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_luar.group_jabatan_luar_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_luar.tanggal_aktif IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_luar.tanggal_nonaktif IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE jabatan_pegawai_luar (id UUID NOT NULL, pegawai_luar_id UUID NOT NULL, jabatan_luar_id UUID NOT NULL, tipe_id UUID DEFAULT NULL, kantor_luar_id UUID NOT NULL, unit_luar_id UUID DEFAULT NULL, atribut_id UUID DEFAULT NULL, referensi VARCHAR(255) DEFAULT NULL, tanggal_mulai TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tanggal_selesai TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C6F9F7EA67E1AAEE ON jabatan_pegawai_luar (pegawai_luar_id)');
        $this->addSql('CREATE INDEX IDX_C6F9F7EABD8C7F77 ON jabatan_pegawai_luar (jabatan_luar_id)');
        $this->addSql('CREATE INDEX IDX_C6F9F7EAC69A8E08 ON jabatan_pegawai_luar (tipe_id)');
        $this->addSql('CREATE INDEX IDX_C6F9F7EAD7819550 ON jabatan_pegawai_luar (kantor_luar_id)');
        $this->addSql('CREATE INDEX IDX_C6F9F7EA22B84197 ON jabatan_pegawai_luar (unit_luar_id)');
        $this->addSql('CREATE INDEX IDX_C6F9F7EA89E8E03D ON jabatan_pegawai_luar (atribut_id)');
        $this->addSql('CREATE INDEX idx_jabatan_pegawai_luar ON jabatan_pegawai_luar (id, tanggal_mulai, tanggal_selesai)');
        $this->addSql('CREATE INDEX idx_jabatan_pegawai_luar_relation ON jabatan_pegawai_luar (id, pegawai_luar_id, jabatan_luar_id, tipe_id, kantor_luar_id, unit_luar_id)');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.pegawai_luar_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.jabatan_luar_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.tipe_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.kantor_luar_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.unit_luar_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.atribut_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.tanggal_mulai IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.tanggal_selesai IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE jenis_kantor_luar (id UUID NOT NULL, nama VARCHAR(255) NOT NULL, tipe VARCHAR(255) NOT NULL, klasifikasi INT NOT NULL, tanggal_aktif TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tanggal_nonaktif TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, legacy_id INT DEFAULT NULL, legacy_kode INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_jenis_kantor_luar_nama_status ON jenis_kantor_luar (id, nama, tipe, klasifikasi)');
        $this->addSql('CREATE INDEX idx_jenis_kantor_luar_legacy ON jenis_kantor_luar (id, legacy_kode, legacy_id)');
        $this->addSql('CREATE INDEX idx_jenis_kantor_luar_active ON jenis_kantor_luar (id, nama, legacy_kode, tanggal_aktif, tanggal_nonaktif)');
        $this->addSql('COMMENT ON COLUMN jenis_kantor_luar.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jenis_kantor_luar.tanggal_aktif IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN jenis_kantor_luar.tanggal_nonaktif IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE kantor_luar (id UUID NOT NULL, jenis_kantor_luar_id UUID NOT NULL, parent_id UUID DEFAULT NULL, pembina_id UUID DEFAULT NULL, nama VARCHAR(255) NOT NULL, level INT DEFAULT NULL, tanggal_aktif TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tanggal_nonaktif TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, sk VARCHAR(255) DEFAULT NULL, alamat TEXT DEFAULT NULL, telp VARCHAR(255) DEFAULT NULL, fax VARCHAR(255) DEFAULT NULL, zona_waktu VARCHAR(4) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, legacy_kode VARCHAR(10) DEFAULT NULL, legacy_kode_kpp VARCHAR(3) DEFAULT NULL, legacy_kode_kanwil VARCHAR(3) DEFAULT NULL, provinsi UUID DEFAULT NULL, kabupaten_kota UUID DEFAULT NULL, kecamatan UUID DEFAULT NULL, kelurahan UUID DEFAULT NULL, provinsi_name VARCHAR(255) DEFAULT NULL, kabupaten_kota_name VARCHAR(255) DEFAULT NULL, kecamatan_name VARCHAR(255) DEFAULT NULL, kelurahan_name VARCHAR(255) DEFAULT NULL, ministry_office_code VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FA9C6AA8E4F219F1 ON kantor_luar (jenis_kantor_luar_id)');
        $this->addSql('CREATE INDEX IDX_FA9C6AA8727ACA70 ON kantor_luar (parent_id)');
        $this->addSql('CREATE INDEX IDX_FA9C6AA84793DA59 ON kantor_luar (pembina_id)');
        $this->addSql('CREATE INDEX idx_kantor_luar_nama_status ON kantor_luar (id, nama, level, sk)');
        $this->addSql('CREATE INDEX idx_kantor_luar_legacy ON kantor_luar (id, legacy_kode, legacy_kode_kpp, legacy_kode_kanwil, ministry_office_code)');
        $this->addSql('CREATE INDEX idx_kantor_luar_relation ON kantor_luar (id, jenis_kantor_luar_id, parent_id, level, pembina_id)');
        $this->addSql('CREATE INDEX idx_kantor_luar_location ON kantor_luar (id, latitude, longitude)');
        $this->addSql('CREATE INDEX idx_kantor_luar_active ON kantor_luar (id, nama, legacy_kode, tanggal_aktif, tanggal_nonaktif)');
        $this->addSql('CREATE INDEX idx_kantor_luar_position ON kantor_luar (id, nama, legacy_kode, provinsi, provinsi_name, kabupaten_kota, kabupaten_kota_name, kecamatan, kecamatan_name, kelurahan, kelurahan_name)');
        $this->addSql('COMMENT ON COLUMN kantor_luar.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.jenis_kantor_luar_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.pembina_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.tanggal_aktif IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.tanggal_nonaktif IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.provinsi IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.kabupaten_kota IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.kecamatan IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor_luar.kelurahan IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE pegawai_luar (id UUID NOT NULL, user_id UUID NOT NULL, nama VARCHAR(255) NOT NULL, pensiun BOOLEAN NOT NULL, npwp VARCHAR(255) DEFAULT NULL, nik VARCHAR(16) DEFAULT NULL, nip18 VARCHAR(18) DEFAULT NULL, pangkat VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F9F486A9A76ED395 ON pegawai_luar (user_id)');
        $this->addSql('CREATE INDEX idx_pegawai_luar_data ON pegawai_luar (id, nama, pensiun, nik, nip18, pangkat)');
        $this->addSql('CREATE INDEX idx_pegawai_luar_legacy ON pegawai_luar (id, nip18)');
        $this->addSql('CREATE INDEX idx_pegawai_luar_relation ON pegawai_luar (id, user_id)');
        $this->addSql('COMMENT ON COLUMN pegawai_luar.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN pegawai_luar.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE role_jabatan_luar (role_id UUID NOT NULL, jabatan_luar_id UUID NOT NULL, PRIMARY KEY(role_id, jabatan_luar_id))');
        $this->addSql('CREATE INDEX IDX_4EF76075D60322AC ON role_jabatan_luar (role_id)');
        $this->addSql('CREATE INDEX IDX_4EF76075BD8C7F77 ON role_jabatan_luar (jabatan_luar_id)');
        $this->addSql('COMMENT ON COLUMN role_jabatan_luar.role_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN role_jabatan_luar.jabatan_luar_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE role_unit_luar (role_id UUID NOT NULL, unit_luar_id UUID NOT NULL, PRIMARY KEY(role_id, unit_luar_id))');
        $this->addSql('CREATE INDEX IDX_1D51456CD60322AC ON role_unit_luar (role_id)');
        $this->addSql('CREATE INDEX IDX_1D51456C22B84197 ON role_unit_luar (unit_luar_id)');
        $this->addSql('COMMENT ON COLUMN role_unit_luar.role_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN role_unit_luar.unit_luar_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE role_kantor_luar (role_id UUID NOT NULL, kantor_luar_id UUID NOT NULL, PRIMARY KEY(role_id, kantor_luar_id))');
        $this->addSql('CREATE INDEX IDX_C8CB3221D60322AC ON role_kantor_luar (role_id)');
        $this->addSql('CREATE INDEX IDX_C8CB3221D7819550 ON role_kantor_luar (kantor_luar_id)');
        $this->addSql('COMMENT ON COLUMN role_kantor_luar.role_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN role_kantor_luar.kantor_luar_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE unit_luar (id UUID NOT NULL, jenis_kantor_luar_id UUID NOT NULL, parent_id UUID DEFAULT NULL, eselon_id UUID NOT NULL, pembina_id UUID DEFAULT NULL, nama VARCHAR(255) NOT NULL, level INT NOT NULL, tanggal_aktif TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tanggal_nonaktif TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, legacy_kode VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4CA596B0E4F219F1 ON unit_luar (jenis_kantor_luar_id)');
        $this->addSql('CREATE INDEX IDX_4CA596B0727ACA70 ON unit_luar (parent_id)');
        $this->addSql('CREATE INDEX IDX_4CA596B02D0196DB ON unit_luar (eselon_id)');
        $this->addSql('CREATE INDEX IDX_4CA596B04793DA59 ON unit_luar (pembina_id)');
        $this->addSql('CREATE INDEX idx_unit_luar_nama ON unit_luar (id, nama, level)');
        $this->addSql('CREATE INDEX idx_unit_luar_legacy ON unit_luar (id, legacy_kode, pembina_id)');
        $this->addSql('CREATE INDEX idx_unit_luar_relation ON unit_luar (id, jenis_kantor_luar_id, parent_id, eselon_id)');
        $this->addSql('CREATE INDEX idx_unit_luar_active ON unit_luar (id, nama, legacy_kode, tanggal_aktif, tanggal_nonaktif)');
        $this->addSql('COMMENT ON COLUMN unit_luar.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unit_luar.jenis_kantor_luar_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unit_luar.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unit_luar.eselon_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unit_luar.pembina_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unit_luar.tanggal_aktif IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN unit_luar.tanggal_nonaktif IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE unit_luar_jabatan_luar (unit_luar_id UUID NOT NULL, jabatan_luar_id UUID NOT NULL, PRIMARY KEY(unit_luar_id, jabatan_luar_id))');
        $this->addSql('CREATE INDEX IDX_A0A770E022B84197 ON unit_luar_jabatan_luar (unit_luar_id)');
        $this->addSql('CREATE INDEX IDX_A0A770E0BD8C7F77 ON unit_luar_jabatan_luar (jabatan_luar_id)');
        $this->addSql('COMMENT ON COLUMN unit_luar_jabatan_luar.unit_luar_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unit_luar_jabatan_luar.jabatan_luar_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE jabatan_luar ADD CONSTRAINT FK_DAA10CA92D0196DB FOREIGN KEY (eselon_id) REFERENCES eselon (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE jabatan_luar ADD CONSTRAINT FK_DAA10CA931FE2AE FOREIGN KEY (group_jabatan_luar_id) REFERENCES group_jabatan_luar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar ADD CONSTRAINT FK_C6F9F7EA67E1AAEE FOREIGN KEY (pegawai_luar_id) REFERENCES pegawai_luar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar ADD CONSTRAINT FK_C6F9F7EABD8C7F77 FOREIGN KEY (jabatan_luar_id) REFERENCES jabatan_luar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar ADD CONSTRAINT FK_C6F9F7EAC69A8E08 FOREIGN KEY (tipe_id) REFERENCES tipe_jabatan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar ADD CONSTRAINT FK_C6F9F7EAD7819550 FOREIGN KEY (kantor_luar_id) REFERENCES kantor_luar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar ADD CONSTRAINT FK_C6F9F7EA22B84197 FOREIGN KEY (unit_luar_id) REFERENCES unit_luar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar ADD CONSTRAINT FK_C6F9F7EA89E8E03D FOREIGN KEY (atribut_id) REFERENCES jabatan_atribut (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE kantor_luar ADD CONSTRAINT FK_FA9C6AA8E4F219F1 FOREIGN KEY (jenis_kantor_luar_id) REFERENCES jenis_kantor_luar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE kantor_luar ADD CONSTRAINT FK_FA9C6AA8727ACA70 FOREIGN KEY (parent_id) REFERENCES kantor_luar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE kantor_luar ADD CONSTRAINT FK_FA9C6AA84793DA59 FOREIGN KEY (pembina_id) REFERENCES kantor_luar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pegawai_luar ADD CONSTRAINT FK_F9F486A9A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE role_jabatan_luar ADD CONSTRAINT FK_4EF76075D60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE role_jabatan_luar ADD CONSTRAINT FK_4EF76075BD8C7F77 FOREIGN KEY (jabatan_luar_id) REFERENCES jabatan_luar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE role_unit_luar ADD CONSTRAINT FK_1D51456CD60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE role_unit_luar ADD CONSTRAINT FK_1D51456C22B84197 FOREIGN KEY (unit_luar_id) REFERENCES unit_luar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE role_kantor_luar ADD CONSTRAINT FK_C8CB3221D60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE role_kantor_luar ADD CONSTRAINT FK_C8CB3221D7819550 FOREIGN KEY (kantor_luar_id) REFERENCES kantor_luar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE unit_luar ADD CONSTRAINT FK_4CA596B0E4F219F1 FOREIGN KEY (jenis_kantor_luar_id) REFERENCES jenis_kantor_luar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE unit_luar ADD CONSTRAINT FK_4CA596B0727ACA70 FOREIGN KEY (parent_id) REFERENCES unit_luar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE unit_luar ADD CONSTRAINT FK_4CA596B02D0196DB FOREIGN KEY (eselon_id) REFERENCES eselon (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE unit_luar ADD CONSTRAINT FK_4CA596B04793DA59 FOREIGN KEY (pembina_id) REFERENCES unit_luar (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE unit_luar_jabatan_luar ADD CONSTRAINT FK_A0A770E022B84197 FOREIGN KEY (unit_luar_id) REFERENCES unit_luar (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE unit_luar_jabatan_luar ADD CONSTRAINT FK_A0A770E0BD8C7F77 FOREIGN KEY (jabatan_luar_id) REFERENCES jabatan_luar (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE jabatan_luar DROP CONSTRAINT FK_DAA10CA92D0196DB');
        $this->addSql('ALTER TABLE jabatan_luar DROP CONSTRAINT FK_DAA10CA931FE2AE');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar DROP CONSTRAINT FK_C6F9F7EA67E1AAEE');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar DROP CONSTRAINT FK_C6F9F7EABD8C7F77');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar DROP CONSTRAINT FK_C6F9F7EAC69A8E08');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar DROP CONSTRAINT FK_C6F9F7EAD7819550');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar DROP CONSTRAINT FK_C6F9F7EA22B84197');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar DROP CONSTRAINT FK_C6F9F7EA89E8E03D');
        $this->addSql('ALTER TABLE kantor_luar DROP CONSTRAINT FK_FA9C6AA8E4F219F1');
        $this->addSql('ALTER TABLE kantor_luar DROP CONSTRAINT FK_FA9C6AA8727ACA70');
        $this->addSql('ALTER TABLE kantor_luar DROP CONSTRAINT FK_FA9C6AA84793DA59');
        $this->addSql('ALTER TABLE pegawai_luar DROP CONSTRAINT FK_F9F486A9A76ED395');
        $this->addSql('ALTER TABLE role_jabatan_luar DROP CONSTRAINT FK_4EF76075D60322AC');
        $this->addSql('ALTER TABLE role_jabatan_luar DROP CONSTRAINT FK_4EF76075BD8C7F77');
        $this->addSql('ALTER TABLE role_unit_luar DROP CONSTRAINT FK_1D51456CD60322AC');
        $this->addSql('ALTER TABLE role_unit_luar DROP CONSTRAINT FK_1D51456C22B84197');
        $this->addSql('ALTER TABLE role_kantor_luar DROP CONSTRAINT FK_C8CB3221D60322AC');
        $this->addSql('ALTER TABLE role_kantor_luar DROP CONSTRAINT FK_C8CB3221D7819550');
        $this->addSql('ALTER TABLE unit_luar DROP CONSTRAINT FK_4CA596B0E4F219F1');
        $this->addSql('ALTER TABLE unit_luar DROP CONSTRAINT FK_4CA596B0727ACA70');
        $this->addSql('ALTER TABLE unit_luar DROP CONSTRAINT FK_4CA596B02D0196DB');
        $this->addSql('ALTER TABLE unit_luar DROP CONSTRAINT FK_4CA596B04793DA59');
        $this->addSql('ALTER TABLE unit_luar_jabatan_luar DROP CONSTRAINT FK_A0A770E022B84197');
        $this->addSql('ALTER TABLE unit_luar_jabatan_luar DROP CONSTRAINT FK_A0A770E0BD8C7F77');
        $this->addSql('DROP TABLE group_jabatan_luar');
        $this->addSql('DROP TABLE jabatan_luar');
        $this->addSql('DROP TABLE jabatan_pegawai_luar');
        $this->addSql('DROP TABLE jenis_kantor_luar');
        $this->addSql('DROP TABLE kantor_luar');
        $this->addSql('DROP TABLE pegawai_luar');
        $this->addSql('DROP TABLE role_jabatan_luar');
        $this->addSql('DROP TABLE role_unit_luar');
        $this->addSql('DROP TABLE role_kantor_luar');
        $this->addSql('DROP TABLE unit_luar');
        $this->addSql('DROP TABLE unit_luar_jabatan_luar');
    }
}
