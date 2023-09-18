<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230918043029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE jabatan_pegawai_luar (id UUID NOT NULL, pegawai_id UUID NOT NULL, jabatan_id UUID NOT NULL, tipe_id UUID DEFAULT NULL, kantor_id UUID NOT NULL, unit_id UUID DEFAULT NULL, atribut_id UUID DEFAULT NULL, referensi VARCHAR(255) DEFAULT NULL, tanggal_mulai TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tanggal_selesai TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C6F9F7EA998300D9 ON jabatan_pegawai_luar (pegawai_id)');
        $this->addSql('CREATE INDEX IDX_C6F9F7EAA7400487 ON jabatan_pegawai_luar (jabatan_id)');
        $this->addSql('CREATE INDEX IDX_C6F9F7EAC69A8E08 ON jabatan_pegawai_luar (tipe_id)');
        $this->addSql('CREATE INDEX IDX_C6F9F7EA2C104734 ON jabatan_pegawai_luar (kantor_id)');
        $this->addSql('CREATE INDEX IDX_C6F9F7EAF8BD700D ON jabatan_pegawai_luar (unit_id)');
        $this->addSql('CREATE INDEX IDX_C6F9F7EA89E8E03D ON jabatan_pegawai_luar (atribut_id)');
        $this->addSql('CREATE INDEX idx_jabatan_pegawai_luar ON jabatan_pegawai_luar (id, tanggal_mulai, tanggal_selesai)');
        $this->addSql('CREATE INDEX idx_jabatan_pegawai_luar_relation ON jabatan_pegawai_luar (id, pegawai_id, jabatan_id, tipe_id, kantor_id, unit_id)');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.pegawai_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.jabatan_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.tipe_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.kantor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.unit_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.atribut_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.tanggal_mulai IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_pegawai_luar.tanggal_selesai IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar ADD CONSTRAINT FK_C6F9F7EA998300D9 FOREIGN KEY (pegawai_id) REFERENCES pegawai (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar ADD CONSTRAINT FK_C6F9F7EAA7400487 FOREIGN KEY (jabatan_id) REFERENCES jabatan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar ADD CONSTRAINT FK_C6F9F7EAC69A8E08 FOREIGN KEY (tipe_id) REFERENCES tipe_jabatan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar ADD CONSTRAINT FK_C6F9F7EA2C104734 FOREIGN KEY (kantor_id) REFERENCES kantor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar ADD CONSTRAINT FK_C6F9F7EAF8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar ADD CONSTRAINT FK_C6F9F7EA89E8E03D FOREIGN KEY (atribut_id) REFERENCES jabatan_atribut (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar DROP CONSTRAINT FK_C6F9F7EA998300D9');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar DROP CONSTRAINT FK_C6F9F7EAA7400487');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar DROP CONSTRAINT FK_C6F9F7EAC69A8E08');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar DROP CONSTRAINT FK_C6F9F7EA2C104734');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar DROP CONSTRAINT FK_C6F9F7EAF8BD700D');
        $this->addSql('ALTER TABLE jabatan_pegawai_luar DROP CONSTRAINT FK_C6F9F7EA89E8E03D');
        $this->addSql('DROP TABLE jabatan_pegawai_luar');
    }
}
