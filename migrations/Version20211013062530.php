<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211013062530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX idx_jabatan_active ON jabatan (id, nama, legacy_kode, tanggal_aktif, tanggal_nonaktif)');
        $this->addSql('CREATE INDEX idx_jenis_kantor_active ON jenis_kantor (id, nama, legacy_kode, tanggal_aktif, tanggal_nonaktif)');
        $this->addSql('CREATE INDEX idx_kantor_active ON kantor (id, nama, legacy_kode, tanggal_aktif, tanggal_nonaktif)');
        $this->addSql('COMMENT ON COLUMN role.jenis IS \'Jenis Relasi Role: 1 => user, 2 => jabatan, 3 => unit, 4 => kantor, 5 => eselon,
             *          6 => jenis kantor, 7 => group, 8 => jabatan + unit, 9 => jabatan + kantor,
             *          10 => jabatan + unit + kantor\'');
        $this->addSql('CREATE INDEX idx_unit_active ON unit (id, nama, legacy_kode, tanggal_aktif, tanggal_nonaktif)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX idx_jabatan_active');
        $this->addSql('DROP INDEX idx_unit_active');
        $this->addSql('DROP INDEX idx_kantor_active');
        $this->addSql('DROP INDEX idx_jenis_kantor_active');
        $this->addSql('COMMENT ON COLUMN role.jenis IS \'Jenis Relasi Role: 1 => user, 2 => jabatan, 3 => unit, 4 => kantor, 5 => eselon,
                     *          6 => jenis kantor, 7 => group, 8 => jabatan + unit, 9 => jabatan + kantor,
                     *          10 => jabatan + unit + kantor\'');
    }
}
