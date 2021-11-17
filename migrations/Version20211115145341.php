<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211115145341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE kantor ADD provinsi UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE kantor ADD kabupaten_kota UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE kantor ADD kecamatan UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE kantor ADD kelurahan UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN kantor.provinsi IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor.kabupaten_kota IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor.kecamatan IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN kantor.kelurahan IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN role.jenis IS \'Jenis Relasi Role: 1 => user, 2 => jabatan, 3 => unit, 4 => kantor, 5 => eselon, 6 => jenis kantor, 7 => group, 8 => jabatan + unit, 9 => jabatan + kantor, 10 => jabatan + unit + kantor\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE kantor DROP provinsi');
        $this->addSql('ALTER TABLE kantor DROP kabupaten_kota');
        $this->addSql('ALTER TABLE kantor DROP kecamatan');
        $this->addSql('ALTER TABLE kantor DROP kelurahan');
        $this->addSql('COMMENT ON COLUMN role.jenis IS \'Jenis Relasi Role: 1 => user, 2 => jabatan, 3 => unit, 4 => kantor, 5 => eselon,
                     *          6 => jenis kantor, 7 => group, 8 => jabatan + unit, 9 => jabatan + kantor,
                     *          10 => jabatan + unit + kantor\'');
    }
}
