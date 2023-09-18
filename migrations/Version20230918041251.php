<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230918041251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE unit_luar (id UUID NOT NULL, jenis_kantor_id UUID NOT NULL, parent_id UUID DEFAULT NULL, eselon_id UUID NOT NULL, pembina_id UUID DEFAULT NULL, nama VARCHAR(255) NOT NULL, level INT NOT NULL, tanggal_aktif TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tanggal_nonaktif TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, legacy_kode VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4CA596B05EB4E08C ON unit_luar (jenis_kantor_id)');
        $this->addSql('CREATE INDEX IDX_4CA596B0727ACA70 ON unit_luar (parent_id)');
        $this->addSql('CREATE INDEX IDX_4CA596B02D0196DB ON unit_luar (eselon_id)');
        $this->addSql('CREATE INDEX IDX_4CA596B04793DA59 ON unit_luar (pembina_id)');
        $this->addSql('CREATE INDEX idx_unit_luar_nama ON unit_luar (id, nama, level)');
        $this->addSql('CREATE INDEX idx_unit_luar_legacy ON unit_luar (id, legacy_kode, pembina_id)');
        $this->addSql('CREATE INDEX idx_unit_luar_relation ON unit_luar (id, jenis_kantor_id, parent_id, eselon_id)');
        $this->addSql('CREATE INDEX idx_unit_luar_active ON unit_luar (id, nama, legacy_kode, tanggal_aktif, tanggal_nonaktif)');
        $this->addSql('COMMENT ON COLUMN unit_luar.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unit_luar.jenis_kantor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unit_luar.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unit_luar.eselon_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unit_luar.pembina_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN unit_luar.tanggal_aktif IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN unit_luar.tanggal_nonaktif IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE unit_luar ADD CONSTRAINT FK_4CA596B05EB4E08C FOREIGN KEY (jenis_kantor_id) REFERENCES jenis_kantor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE unit_luar ADD CONSTRAINT FK_4CA596B0727ACA70 FOREIGN KEY (parent_id) REFERENCES unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE unit_luar ADD CONSTRAINT FK_4CA596B02D0196DB FOREIGN KEY (eselon_id) REFERENCES eselon (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE unit_luar ADD CONSTRAINT FK_4CA596B04793DA59 FOREIGN KEY (pembina_id) REFERENCES unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE unit_luar DROP CONSTRAINT FK_4CA596B05EB4E08C');
        $this->addSql('ALTER TABLE unit_luar DROP CONSTRAINT FK_4CA596B0727ACA70');
        $this->addSql('ALTER TABLE unit_luar DROP CONSTRAINT FK_4CA596B02D0196DB');
        $this->addSql('ALTER TABLE unit_luar DROP CONSTRAINT FK_4CA596B04793DA59');
        $this->addSql('DROP TABLE unit_luar');
    }
}
