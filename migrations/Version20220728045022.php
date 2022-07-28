<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220728045022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD service_account BOOLEAN DEFAULT NULL');
        $this->addSql('UPDATE "user" SET service_account = false');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN service_account SET NOT NULL');
        $this->addSql('CREATE INDEX idx_user_service_account ON "user" (id, username, service_account)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX idx_user_service_account');
        $this->addSql('ALTER TABLE "user" DROP service_account');
    }
}
