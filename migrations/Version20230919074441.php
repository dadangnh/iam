<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230919074441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pegawai_luar DROP CONSTRAINT fk_f9f486a9a76ed395');
        $this->addSql('DROP INDEX uniq_f9f486a9a76ed395');
        $this->addSql('DROP INDEX idx_pegawai_luar_relation');
        $this->addSql('ALTER TABLE pegawai_luar ADD user_luar_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE pegawai_luar DROP user_id');
        $this->addSql('COMMENT ON COLUMN pegawai_luar.user_luar_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE pegawai_luar ADD CONSTRAINT FK_F9F486A92EFC515F FOREIGN KEY (user_luar_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F9F486A92EFC515F ON pegawai_luar (user_luar_id)');
        $this->addSql('CREATE INDEX idx_pegawai_luar_relation ON pegawai_luar (id, user_luar_id)');
        $this->addSql('ALTER TABLE role ALTER operator SET DEFAULT false');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pegawai_luar DROP CONSTRAINT FK_F9F486A92EFC515F');
        $this->addSql('DROP INDEX UNIQ_F9F486A92EFC515F');
        $this->addSql('DROP INDEX idx_pegawai_luar_relation');
        $this->addSql('ALTER TABLE pegawai_luar ADD user_id UUID NOT NULL');
        $this->addSql('ALTER TABLE pegawai_luar DROP user_luar_id');
        $this->addSql('COMMENT ON COLUMN pegawai_luar.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE pegawai_luar ADD CONSTRAINT fk_f9f486a9a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_f9f486a9a76ed395 ON pegawai_luar (user_id)');
        $this->addSql('CREATE INDEX idx_pegawai_luar_relation ON pegawai_luar (id, user_id)');
        $this->addSql('ALTER TABLE role ALTER operator DROP DEFAULT');
    }
}
