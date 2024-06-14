<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240612212747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO nines_feedback_comment_status (`name`, `label`, `description`, `created`, `updated`) VALUES ("submitted", "Submitted", "The challenged has been submitted.", NOW(), NOW())');
        $this->addSql('INSERT INTO nines_feedback_comment_status (`name`, `label`, `description`, `created`, `updated`) VALUES ("reviewed", "Reviewed", "The challenged has been reviewed.", NOW(), NOW())');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('REMOVE FROM nines_feedback_comment_status WHERE `name` = "submitted"');
        $this->addSql('REMOVE FROM nines_feedback_comment_status WHERE `name` = "reviewed"');
    }
}
