<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200924214857 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E6519AE39');
        $this->addSql('DROP INDEX IDX_1F1B251E6519AE39 ON item');
        $this->addSql('ALTER TABLE item ADD civilization_other LONGTEXT DEFAULT NULL, ADD findspot_other LONGTEXT DEFAULT NULL, ADD provenance_other LONGTEXT DEFAULT NULL, CHANGE dimensions dimensions LONGTEXT DEFAULT NULL, CHANGE find_spot_id findspot_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E8E893A58 FOREIGN KEY (findspot_id) REFERENCES location (id)');
        $this->addSql('CREATE INDEX IDX_1F1B251E8E893A58 ON item (findspot_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E8E893A58');
        $this->addSql('DROP INDEX IDX_1F1B251E8E893A58 ON item');
        $this->addSql('ALTER TABLE item DROP civilization_other, DROP findspot_other, DROP provenance_other, CHANGE dimensions dimensions VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE findspot_id find_spot_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E6519AE39 FOREIGN KEY (find_spot_id) REFERENCES location (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1F1B251E6519AE39 ON item (find_spot_id)');
    }
}
