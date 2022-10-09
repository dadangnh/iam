<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221009053734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE kantor ADD provinsi_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE kantor ADD kabupaten_kota_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE kantor ADD kecamatan_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE kantor ADD kelurahan_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_kantor_position ON kantor (id, nama, legacy_kode, provinsi, provinsi_name, kabupaten_kota, kabupaten_kota_name, kecamatan, kecamatan_name, kelurahan, kelurahan_name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX idx_kantor_position');
        $this->addSql('ALTER TABLE kantor DROP provinsi_name');
        $this->addSql('ALTER TABLE kantor DROP kabupaten_kota_name');
        $this->addSql('ALTER TABLE kantor DROP kecamatan_name');
        $this->addSql('ALTER TABLE kantor DROP kelurahan_name');
    }
}
