<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230731095025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE geonames_administrative_division_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE geonames_administrative_division (id INT NOT NULL, geoname_id INT NOT NULL, name VARCHAR(255) NOT NULL, ascii_name VARCHAR(255) DEFAULT NULL, toponym_name VARCHAR(255) DEFAULT NULL, continent_code VARCHAR(255) DEFAULT NULL, cc2 VARCHAR(255) DEFAULT NULL, country_code VARCHAR(255) DEFAULT NULL, admin_name1 VARCHAR(255) DEFAULT NULL, admin_name2 VARCHAR(255) DEFAULT NULL, admin_name3 VARCHAR(255) DEFAULT NULL, admin_name4 VARCHAR(255) DEFAULT NULL, admin_name5 VARCHAR(255) DEFAULT NULL, admin_id1 INT DEFAULT NULL, admin_id2 INT DEFAULT NULL, admin_id3 INT DEFAULT NULL, admin_id4 INT DEFAULT NULL, admin_id5 INT DEFAULT NULL, admin_code1 INT DEFAULT NULL, admin_code2 INT DEFAULT NULL, admin_code3 INT DEFAULT NULL, lat NUMERIC(10, 6) DEFAULT NULL, lng NUMERIC(10, 6) DEFAULT NULL, population INT DEFAULT NULL, timezone_gmt_offset INT DEFAULT NULL, timezone_time_zone_id VARCHAR(255) DEFAULT NULL, timezone_dst_offset INT DEFAULT NULL, admin_type_name VARCHAR(255) DEFAULT NULL, fcode VARCHAR(255) DEFAULT NULL, geojson VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE geonames_administrative_division_id_seq CASCADE');
        $this->addSql('DROP TABLE geonames_administrative_division');
    }
}
