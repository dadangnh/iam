<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221128100034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role ADD start_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE role ADD end_date DATE DEFAULT NULL');
        $this->addSql('UPDATE role SET start_date = \'2020-01-01\'');
        $this->addSql('ALTER TABLE role ALTER COLUMN start_date SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN role.start_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN role.end_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE INDEX idx_role_date ON role (id, nama, system_name, jenis, start_date, end_date)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_role_date');
        $this->addSql('ALTER TABLE role DROP start_date');
        $this->addSql('ALTER TABLE role DROP end_date');
    }
}
