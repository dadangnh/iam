<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241119073912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE jabatan ADD nama_eng VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE kantor ADD nama_eng VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE unit ADD nama_eng VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE kantor DROP nama_eng');
        $this->addSql('ALTER TABLE unit DROP nama_eng');
        $this->addSql('ALTER TABLE jabatan DROP nama_eng');
    }
}
