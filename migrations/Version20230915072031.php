<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230915072031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE jabatan_luar (id UUID NOT NULL, eselon_id UUID DEFAULT NULL, group_jabatan_id UUID DEFAULT NULL, nama VARCHAR(255) NOT NULL, level INT NOT NULL, jenis VARCHAR(255) NOT NULL, tanggal_aktif TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tanggal_nonaktif TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, sk VARCHAR(255) DEFAULT NULL, legacy_kode VARCHAR(4) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DAA10CA92D0196DB ON jabatan_luar (eselon_id)');
        $this->addSql('CREATE INDEX IDX_DAA10CA9708D6535 ON jabatan_luar (group_jabatan_id)');
        $this->addSql('CREATE INDEX idx_jabatan_luar_nama_status ON jabatan_luar (id, nama, level, jenis)');
        $this->addSql('CREATE INDEX idx_jabatan_luar_legacy ON jabatan_luar (id, legacy_kode)');
        $this->addSql('CREATE INDEX idx_jabatan_luar_relation ON jabatan_luar (id, eselon_id)');
        $this->addSql('CREATE INDEX idx_jabatan_luar_active ON jabatan_luar (id, nama, legacy_kode, tanggal_aktif, tanggal_nonaktif)');
        $this->addSql('COMMENT ON COLUMN jabatan_luar.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_luar.eselon_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_luar.group_jabatan_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_luar.tanggal_aktif IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN jabatan_luar.tanggal_nonaktif IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE jabatan_luar ADD CONSTRAINT FK_DAA10CA92D0196DB FOREIGN KEY (eselon_id) REFERENCES eselon (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE jabatan_luar ADD CONSTRAINT FK_DAA10CA9708D6535 FOREIGN KEY (group_jabatan_id) REFERENCES group_jabatan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE jabatan_luar DROP CONSTRAINT FK_DAA10CA92D0196DB');
        $this->addSql('ALTER TABLE jabatan_luar DROP CONSTRAINT FK_DAA10CA9708D6535');
        $this->addSql('DROP TABLE jabatan_luar');
    }
}
