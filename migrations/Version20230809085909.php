<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230809085909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS geonames_country_level_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE IF NOT EXISTS geonames_country_level (id INT NOT NULL, country_code VARCHAR(10) NOT NULL, max_level INT NOT NULL, used_level INT NOT NULL, adm1 INT NOT NULL, adm2 INT NOT NULL, adm3 INT NOT NULL, adm4 INT NOT NULL, adm5 INT NOT NULL, done VARCHAR(1) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('DROP SEQUENCE IF EXISTS geonames_country_level_id_seq CASCADE');
        $this->addSql('DROP TABLE IF EXISTS geonames_country_level');
    }
}
