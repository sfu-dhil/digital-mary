<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203214000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // rename tables
        $this->addSql('RENAME TABLE category to digital_mary_category');
        $this->addSql('RENAME TABLE civilization to digital_mary_culture');
        $this->addSql('RENAME TABLE contribution to digital_mary_contribution');
        $this->addSql('RENAME TABLE image to digital_mary_image');
        $this->addSql('RENAME TABLE inscription_style to digital_mary_inscription_style');
        $this->addSql('RENAME TABLE item to digital_mary_item');
        $this->addSql('RENAME TABLE item_category to digital_mary_item_categories');
        $this->addSql('RENAME TABLE item_civilization to digital_mary_item_cultures');
        $this->addSql('RENAME TABLE item_language to digital_mary_item_languages');
        $this->addSql('RENAME TABLE item_material to digital_mary_item_materials');
        $this->addSql('RENAME TABLE item_subject to digital_mary_item_subjects');
        $this->addSql('RENAME TABLE item_technique to digital_mary_item_techniques');
        $this->addSql('RENAME TABLE language to digital_mary_language');
        $this->addSql('RENAME TABLE location to digital_mary_location');
        $this->addSql('RENAME TABLE material to digital_mary_material');
        // period
        $this->addSql('RENAME TABLE person to digital_mary_person');
        $this->addSql('RENAME TABLE remote_image to digital_mary_remote_image');
        $this->addSql('RENAME TABLE subject to digital_mary_subject');
        $this->addSql('RENAME TABLE technique to digital_mary_technique');

        $this->addSql('RENAME TABLE nines_user to auth_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}