<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240612212746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE nines_feedback_comment (id INT AUTO_INCREMENT NOT NULL, status_id INT NOT NULL, fullname VARCHAR(120) NOT NULL, email VARCHAR(120) NOT NULL, follow_up TINYINT(1) NOT NULL, content LONGTEXT NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', entity VARCHAR(120) NOT NULL, INDEX IDX_DD5C8DB56BF700BD (status_id), FULLTEXT INDEX comment_ft (fullname, content), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nines_feedback_comment_note (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, comment_id INT NOT NULL, content LONGTEXT NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4BC0F0BA76ED395 (user_id), INDEX IDX_4BC0F0BF8697D13 (comment_id), FULLTEXT INDEX comment_note_ft (content), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nines_feedback_comment_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(191) NOT NULL, label VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', FULLTEXT INDEX IDX_7B8DA610EA750E8 (label), FULLTEXT INDEX IDX_7B8DA6106DE44026 (description), FULLTEXT INDEX IDX_7B8DA610EA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_7B8DA6105E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nines_feedback_comment ADD CONSTRAINT FK_DD5C8DB56BF700BD FOREIGN KEY (status_id) REFERENCES nines_feedback_comment_status (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE nines_feedback_comment_note ADD CONSTRAINT FK_4BC0F0BA76ED395 FOREIGN KEY (user_id) REFERENCES nines_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE nines_feedback_comment_note ADD CONSTRAINT FK_4BC0F0BF8697D13 FOREIGN KEY (comment_id) REFERENCES nines_feedback_comment (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nines_feedback_comment DROP FOREIGN KEY FK_DD5C8DB56BF700BD');
        $this->addSql('ALTER TABLE nines_feedback_comment_note DROP FOREIGN KEY FK_4BC0F0BA76ED395');
        $this->addSql('ALTER TABLE nines_feedback_comment_note DROP FOREIGN KEY FK_4BC0F0BF8697D13');
        $this->addSql('DROP TABLE nines_feedback_comment');
        $this->addSql('DROP TABLE nines_feedback_comment_note');
        $this->addSql('DROP TABLE nines_feedback_comment_status');
    }
}
