<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230807092330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE geonames_administrative_division ADD country_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE geonames_administrative_division ADD fcl VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE geonames_administrative_division ADD srtm3 INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE geonames_administrative_division DROP country_id');
        $this->addSql('ALTER TABLE geonames_administrative_division DROP fcl');
        $this->addSql('ALTER TABLE geonames_administrative_division DROP srtm3');
    }
}
