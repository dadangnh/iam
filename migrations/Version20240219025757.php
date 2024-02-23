<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240219025757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE role_aplikasi (role_id UUID NOT NULL, aplikasi_id UUID NOT NULL, PRIMARY KEY(role_id, aplikasi_id))');
        $this->addSql('CREATE INDEX IDX_C182B197D60322AC ON role_aplikasi (role_id)');
        $this->addSql('CREATE INDEX IDX_C182B19752224EF8 ON role_aplikasi (aplikasi_id)');
        $this->addSql('COMMENT ON COLUMN role_aplikasi.role_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN role_aplikasi.aplikasi_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE role_aplikasi ADD CONSTRAINT FK_C182B197D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE role_aplikasi ADD CONSTRAINT FK_C182B19752224EF8 FOREIGN KEY (aplikasi_id) REFERENCES aplikasi (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE role_aplikasi DROP CONSTRAINT FK_C182B197D60322AC');
        $this->addSql('ALTER TABLE role_aplikasi DROP CONSTRAINT FK_C182B19752224EF8');
        $this->addSql('DROP TABLE role_aplikasi');
    }
}
