<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203213114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_64C19C15E237E06 ON category');
        $this->addSql('ALTER TABLE category DROP name');
        $this->addSql('DROP INDEX UNIQ_78A222C15E237E06 ON civilization');
        $this->addSql('ALTER TABLE civilization DROP name');
        $this->addSql('DROP INDEX UNIQ_61F4EC275E237E06 ON inscription_style');
        $this->addSql('ALTER TABLE inscription_style DROP name');
        $this->addSql('DROP INDEX UNIQ_D4DB71B55E237E06 ON language');
        $this->addSql('ALTER TABLE language DROP name');
        $this->addSql('DROP INDEX UNIQ_5E9E89CB5E237E06 ON location');
        $this->addSql('ALTER TABLE location DROP name, CHANGE latitude latitude DOUBLE PRECISION DEFAULT NULL, CHANGE longitude longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_7CBE75955E237E06 ON material');
        $this->addSql('ALTER TABLE material DROP name');
        $this->addSql('DROP INDEX UNIQ_C5B81ECE5E237E06 ON period');
        $this->addSql('ALTER TABLE period DROP name');
        $this->addSql('DROP INDEX UNIQ_FBCE3E7A5E237E06 ON subject');
        $this->addSql('ALTER TABLE subject DROP name');
        $this->addSql('DROP INDEX UNIQ_D73B98415E237E06 ON technique');
        $this->addSql('ALTER TABLE technique DROP name');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject ADD name VARCHAR(191) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FBCE3E7A5E237E06 ON subject (name)');
        $this->addSql('ALTER TABLE inscription_style ADD name VARCHAR(191) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_61F4EC275E237E06 ON inscription_style (name)');
        $this->addSql('ALTER TABLE period ADD name VARCHAR(191) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C5B81ECE5E237E06 ON period (name)');
        $this->addSql('ALTER TABLE language ADD name VARCHAR(191) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4DB71B55E237E06 ON language (name)');
        $this->addSql('ALTER TABLE location ADD name VARCHAR(191) NOT NULL, CHANGE latitude latitude NUMERIC(10, 7) DEFAULT NULL, CHANGE longitude longitude NUMERIC(10, 7) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5E9E89CB5E237E06 ON location (name)');
        $this->addSql('ALTER TABLE civilization ADD name VARCHAR(191) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_78A222C15E237E06 ON civilization (name)');
        $this->addSql('ALTER TABLE technique ADD name VARCHAR(191) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D73B98415E237E06 ON technique (name)');
        $this->addSql('ALTER TABLE category ADD name VARCHAR(191) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C15E237E06 ON category (name)');
        $this->addSql('ALTER TABLE material ADD name VARCHAR(191) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7CBE75955E237E06 ON material (name)');
    }
}
