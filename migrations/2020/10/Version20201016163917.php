<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201016163917 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E77CD6F52');
        $this->addSql('DROP INDEX UNIQ_1F1B251E77CD6F52 ON item');
        $this->addSql('ALTER TABLE item ADD display_year VARCHAR(60) DEFAULT NULL, ADD gregorian_year INT DEFAULT NULL');
        $this->addSql('update dm.item i set i.display_year = (select value from dm.circa_date cd where i.circa_date_id = cd.id)');
        $this->addSql('update item set display_year=null where display_year=\'1\'');
        $this->addSql('ALTER TABLE item DROP circa_date_id');
        $this->addSql('DROP TABLE circa_date');
    }

    public function postUp(Schema $schema) : void {
        parent::postUp($schema);
    }

    public function down(Schema $schema) : void
    {
        $this->throwIrreversibleMigrationException();
    }
}
