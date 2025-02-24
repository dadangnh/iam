<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241112011605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_jabatan_atribut_nama');
        $this->addSql('CREATE INDEX idx_jabatan_atribut_nama ON jabatan_atribut (id, nama_atribut)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX idx_jabatan_atribut_nama');
        $this->addSql('CREATE INDEX idx_jabatan_atribut_nama ON jabatan_atribut (id)');
    }
}
