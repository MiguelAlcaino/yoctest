<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180721204933 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE city (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, timezone VARCHAR(255) NOT NULL, country_code VARCHAR(4) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D5B0234F026BB7C5E237E06 ON city (country_code, name)');
        $this->addSql('CREATE TABLE weather_record_daily (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, city_id INTEGER NOT NULL, max_temp NUMERIC(5, 2) NOT NULL, datetime DATETIME NOT NULL, "temp" NUMERIC(5, 2) NOT NULL, min_temp NUMERIC(5, 2) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_17356F88BAC62AF ON weather_record_daily (city_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_17356F88BAC62AF93F3C6CA ON weather_record_daily (city_id, datetime)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE weather_record_daily');
    }
}
