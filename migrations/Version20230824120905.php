<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230824120905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE geonames_translation_id_seq CASCADE');
        $this->addSql('DROP TABLE geonames_translation');
        $this->addSql('ALTER TABLE geonames_administrative_division ALTER lat TYPE NUMERIC(20, 15)');
        $this->addSql('ALTER TABLE geonames_administrative_division ALTER lng TYPE NUMERIC(20, 15)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE geonames_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE geonames_translation (id INT NOT NULL, geoname_id INT NOT NULL, name VARCHAR(255) NOT NULL, country_code VARCHAR(10) NOT NULL, fcode VARCHAR(255) NOT NULL, locale VARCHAR(5) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE geonames_administrative_division ALTER lat TYPE NUMERIC(10, 6)');
        $this->addSql('ALTER TABLE geonames_administrative_division ALTER lng TYPE NUMERIC(10, 6)');
    }
}
