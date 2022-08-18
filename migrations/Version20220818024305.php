<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220818024305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "group" ADD CONSTRAINT FK_6DC044C57E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6DC044C57E3C61F9 ON "group" (owner_id)');
        $this->addSql('ALTER TABLE "group" ADD PRIMARY KEY (id)');
        $this->addSql('CREATE INDEX idx_group_data ON "group" (id, nama, system_name, status)');
        $this->addSql('CREATE INDEX idx_group_relation ON "group" (id, owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username)');
        $this->addSql('ALTER TABLE "user" ADD PRIMARY KEY (id)');
        $this->addSql('CREATE INDEX idx_user_data ON "user" (id, username, password)');
        $this->addSql('CREATE INDEX idx_user_active ON "user" (id, active, locked)');
        $this->addSql('CREATE INDEX idx_user_service_account ON "user" (id, username, service_account)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "group" DROP CONSTRAINT FK_6DC044C57E3C61F9');
        $this->addSql('DROP INDEX IDX_6DC044C57E3C61F9');
        $this->addSql('DROP INDEX "primary"');
        $this->addSql('DROP INDEX idx_group_data');
        $this->addSql('DROP INDEX idx_group_relation');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677');
        $this->addSql('DROP INDEX "primary"');
        $this->addSql('DROP INDEX idx_user_data');
        $this->addSql('DROP INDEX idx_user_active');
        $this->addSql('DROP INDEX idx_user_service_account');
    }
}
