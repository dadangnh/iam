<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221201023104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_kantor_legacy');
        $this->addSql('ALTER TABLE kantor ADD ministry_office_code VARCHAR(10) DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_kantor_legacy ON kantor (id, legacy_kode, legacy_kode_kpp, legacy_kode_kanwil, ministry_office_code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_kantor_legacy');
        $this->addSql('ALTER TABLE kantor DROP ministry_office_code');
        $this->addSql('CREATE INDEX idx_kantor_legacy ON kantor (id, legacy_kode, legacy_kode_kpp, legacy_kode_kanwil)');
    }
}
