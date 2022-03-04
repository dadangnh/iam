<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220304145020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_pegawai_data');
        $this->addSql('ALTER TABLE pegawai ADD pangkat VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_pegawai_data ON pegawai (id, nama, pensiun, nik, nip9, nip18, pangkat)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX idx_pegawai_data');
        $this->addSql('ALTER TABLE pegawai DROP pangkat');
        $this->addSql('CREATE INDEX idx_pegawai_data ON pegawai (id, nama, pensiun, nik, nip9, nip18)');
    }
}
