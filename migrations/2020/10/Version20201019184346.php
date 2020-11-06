<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201019184346 extends AbstractMigration {
    public function getDescription() : string {
        return '';
    }

    public function up(Schema $schema) : void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE period (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, label VARCHAR(120) NOT NULL, description LONGTEXT DEFAULT NULL, sortable_year INT NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, FULLTEXT INDEX IDX_C5B81ECEEA750E8 (label), FULLTEXT INDEX IDX_C5B81ECE6DE44026 (description), FULLTEXT INDEX IDX_C5B81ECEEA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_C5B81ECE5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE item ADD period_end_id INT DEFAULT NULL, ADD period_start_id INT DEFAULT NULL, DROP gregorian_year');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E776C9F8 FOREIGN KEY (period_start_id) REFERENCES period (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EDF54B74F FOREIGN KEY (period_end_id) REFERENCES period (id)');
        $this->addSql('CREATE INDEX IDX_1F1B251E776C9F8 ON item (period_start_id)');
        $this->addSql('CREATE INDEX IDX_1F1B251EDF54B74F ON item (period_end_id)');
    }

    public function down(Schema $schema) : void {
        $this->throwIrreversibleMigrationException();
    }
}
