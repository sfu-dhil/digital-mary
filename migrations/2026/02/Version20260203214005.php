<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203214005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Fix digital_mary_contribution table columns

        // id
        $this->addSql('ALTER TABLE digital_mary_contribution CHANGE `roles` `marc_relators` longtext');
        $this->addSql("UPDATE digital_mary_contribution SET marc_relators = REPLACE( REPLACE(  marc_relators, '[', '{' ), ']', '}')");
        // created
        // updated
        // item_id
        // person_id
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}