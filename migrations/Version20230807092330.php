<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230807092330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Table geonames_administrative_division';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE geonames_administrative_division (id INT NOT NULL, geoname_id INT NOT NULL, name VARCHAR(255) NOT NULL, ascii_name VARCHAR(255) DEFAULT NULL, toponym_name VARCHAR(255) DEFAULT NULL, continent_code VARCHAR(255) DEFAULT NULL, cc2 VARCHAR(255) DEFAULT NULL, country_code VARCHAR(255) DEFAULT NULL, country_id INT DEFAULT NULL, admin_name1 VARCHAR(255) DEFAULT NULL, admin_name2 VARCHAR(255) DEFAULT NULL, admin_name3 VARCHAR(255) DEFAULT NULL, admin_name4 VARCHAR(255) DEFAULT NULL, admin_name5 VARCHAR(255) DEFAULT NULL, admin_id1 INT DEFAULT NULL, admin_id2 INT DEFAULT NULL, admin_id3 INT DEFAULT NULL, admin_id4 INT DEFAULT NULL, admin_id5 INT DEFAULT NULL, admin_code1 VARCHAR(255) DEFAULT NULL, admin_code2 VARCHAR(255) DEFAULT NULL, admin_code3 VARCHAR(255) DEFAULT NULL, admin_code4 VARCHAR(255) DEFAULT NULL, lat NUMERIC(10, 6) DEFAULT NULL, lng NUMERIC(10, 6) DEFAULT NULL, population INT DEFAULT NULL, timezone_gmt_offset INT DEFAULT NULL, timezone_time_zone_id VARCHAR(255) DEFAULT NULL, timezone_dst_offset INT DEFAULT NULL, admin_type_name VARCHAR(255) DEFAULT NULL, fcode VARCHAR(255) DEFAULT NULL, geojson VARCHAR(255) DEFAULT NULL, fcl VARCHAR(10) DEFAULT NULL, srtm3 INT DEFAULT NULL, astergdem INT DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE geonames_administrative_division');
    }
}
