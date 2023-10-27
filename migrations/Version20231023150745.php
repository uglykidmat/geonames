<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231023150745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS administrative_division_locale_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE IF NOT EXISTS administrative_division_locale (id INT NOT NULL, geoname_id INT NOT NULL, locale VARCHAR(255) NOT NULL, country_code VARCHAR(255) NOT NULL, f_code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, is_preferred_name BOOLEAN DEFAULT NULL, is_short_name BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('DROP SEQUENCE IF EXISTS administrative_division_locale_id_seq CASCADE');
        $this->addSql('DROP TABLE IF EXISTS administrative_division_locale');
    }
}
