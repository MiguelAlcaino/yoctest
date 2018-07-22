<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180722151746 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE country (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, country_code VARCHAR(5) NOT NULL)');
        $this->addSql('DROP INDEX UNIQ_2D5B0234F026BB7C5E237E06');
        $this->addSql('CREATE TEMPORARY TABLE __temp__city AS SELECT id, name, timezone FROM city');
        $this->addSql('DROP TABLE city');
        $this->addSql('CREATE TABLE city (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, country_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, timezone VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_2D5B0234F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO city (id, name, timezone) SELECT id, name, timezone FROM __temp__city');
        $this->addSql('DROP TABLE __temp__city');
        $this->addSql('CREATE INDEX IDX_2D5B0234F92F3E70 ON city (country_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D5B0234F92F3E705E237E06 ON city (country_id, name)');
        $this->addSql('DROP INDEX UNIQ_17356F88BAC62AF93F3C6CA');
        $this->addSql('DROP INDEX IDX_17356F88BAC62AF');
        $this->addSql('CREATE TEMPORARY TABLE __temp__weather_record_daily AS SELECT id, city_id, max_temp, datetime, "temp", min_temp FROM weather_record_daily');
        $this->addSql('DROP TABLE weather_record_daily');
        $this->addSql('CREATE TABLE weather_record_daily (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, city_id INTEGER NOT NULL, max_temp NUMERIC(5, 2) NOT NULL, datetime DATETIME NOT NULL, "temp" NUMERIC(5, 2) NOT NULL, min_temp NUMERIC(5, 2) NOT NULL, CONSTRAINT FK_17356F88BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO weather_record_daily (id, city_id, max_temp, datetime, "temp", min_temp) SELECT id, city_id, max_temp, datetime, "temp", min_temp FROM __temp__weather_record_daily');
        $this->addSql('DROP TABLE __temp__weather_record_daily');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_17356F88BAC62AF93F3C6CA ON weather_record_daily (city_id, datetime)');
        $this->addSql('CREATE INDEX IDX_17356F88BAC62AF ON weather_record_daily (city_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE country');
        $this->addSql('DROP INDEX IDX_2D5B0234F92F3E70');
        $this->addSql('DROP INDEX UNIQ_2D5B0234F92F3E705E237E06');
        $this->addSql('CREATE TEMPORARY TABLE __temp__city AS SELECT id, name, timezone FROM city');
        $this->addSql('DROP TABLE city');
        $this->addSql('CREATE TABLE city (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, timezone VARCHAR(255) NOT NULL, country_code VARCHAR(4) NOT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO city (id, name, timezone) SELECT id, name, timezone FROM __temp__city');
        $this->addSql('DROP TABLE __temp__city');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D5B0234F026BB7C5E237E06 ON city (country_code, name)');
        $this->addSql('DROP INDEX IDX_17356F88BAC62AF');
        $this->addSql('DROP INDEX UNIQ_17356F88BAC62AF93F3C6CA');
        $this->addSql('CREATE TEMPORARY TABLE __temp__weather_record_daily AS SELECT id, city_id, max_temp, datetime, "temp", min_temp FROM weather_record_daily');
        $this->addSql('DROP TABLE weather_record_daily');
        $this->addSql('CREATE TABLE weather_record_daily (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, city_id INTEGER NOT NULL, max_temp NUMERIC(5, 2) NOT NULL, datetime DATETIME NOT NULL, "temp" NUMERIC(5, 2) NOT NULL, min_temp NUMERIC(5, 2) NOT NULL)');
        $this->addSql('INSERT INTO weather_record_daily (id, city_id, max_temp, datetime, "temp", min_temp) SELECT id, city_id, max_temp, datetime, "temp", min_temp FROM __temp__weather_record_daily');
        $this->addSql('DROP TABLE __temp__weather_record_daily');
        $this->addSql('CREATE INDEX IDX_17356F88BAC62AF ON weather_record_daily (city_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_17356F88BAC62AF93F3C6CA ON weather_record_daily (city_id, datetime)');
    }
}
