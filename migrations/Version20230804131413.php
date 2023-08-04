<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230804131413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE geonames_country (id INT NOT NULL, continent VARCHAR(10) DEFAULT NULL, country_code VARCHAR(10) DEFAULT NULL, capital VARCHAR(255) DEFAULT NULL, languages VARCHAR(255) DEFAULT NULL, geoname_id INT NOT NULL, south DOUBLE PRECISION NOT NULL, north DOUBLE PRECISION NOT NULL, east DOUBLE PRECISION NOT NULL, west DOUBLE PRECISION NOT NULL, iso_alpha3 VARCHAR(10) DEFAULT NULL, fips_code VARCHAR(10) DEFAULT NULL, population INT NOT NULL, iso_numeric INT DEFAULT NULL, area_in_sq_km VARCHAR(10) DEFAULT NULL, country_name VARCHAR(255) DEFAULT NULL, continent_name VARCHAR(255) DEFAULT NULL, currency_code VARCHAR(10) DEFAULT NULL, lat NUMERIC(10, 6) DEFAULT NULL, lng NUMERIC(10, 6) DEFAULT NULL, geojson VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE geonames_country');
    }
}
