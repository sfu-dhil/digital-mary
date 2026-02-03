<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203214006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Fix digital_mary_item table columns

        // id
        // name
        // description
        // inscription
        // translated_inscription
        // location
        // dimensions
        $this->addSql('ALTER TABLE digital_mary_item CHANGE `bibliography` `bibliographic_references` longtext');
        $this->addSql('ALTER TABLE digital_mary_item CHANGE `display_year` `display_date` varchar(60)');
        $this->addSql('ALTER TABLE digital_mary_item CHANGE `period_start_id` `earliest_creation` int(11)');
        $this->addSql('ALTER TABLE digital_mary_item CHANGE `period_end_id` `latest_creation` int(11)');
        $this->addSql('ALTER TABLE digital_mary_item CHANGE `civilization_other` `culture_other` longtext');
        $this->addSql('ALTER TABLE digital_mary_item ADD is_public tinyint(1) DEFAULT True');
        // findspot_other
        // provenance_other
        // i18n
        // created
        // updated
        // inscription_style_id
        // findspot_id
        // provenance_id

        $this->addSql('ALTER TABLE digital_mary_item DROP `revisions`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}