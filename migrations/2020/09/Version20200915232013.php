<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200915232013 extends AbstractMigration {
    public function getDescription() : string {
        return '';
    }

    public function up(Schema $schema) : void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE FULLTEXT INDEX IDX_C53D045F545615306DE44026 ON image (original_name, description)');
        $this->addSql('ALTER TABLE remote_image DROP is_image');
        $this->addSql('CREATE FULLTEXT INDEX IDX_3AB79783F47645AE2B36786B6DE44026 ON remote_image (url, title, description)');
    }

    public function down(Schema $schema) : void {
        $this->throwIrreversibleMigrationException();
    }
}
