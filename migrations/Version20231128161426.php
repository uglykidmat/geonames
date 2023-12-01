<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231128161426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE IF EXISTS geonames_country ADD level_id INT NULL');
        $this->addSql('ALTER TABLE IF EXISTS geonames_country ADD CONSTRAINT FK_E3A814F05FB14BA7 FOREIGN KEY (level_id) REFERENCES geonames_country_level (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E3A814F05FB14BA7 ON geonames_country (level_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('ALTER TABLE IF EXISTS geonames_country DROP CONSTRAINT FK_E3A814F05FB14BA7');
        $this->addSql('DROP INDEX UNIQ_E3A814F05FB14BA7');
        $this->addSql('ALTER TABLE IF EXISTS geonames_country DROP level_id');
    }
}
