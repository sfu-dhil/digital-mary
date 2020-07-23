<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200723213314 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE remote_image (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, url VARCHAR(255) NOT NULL, is_image TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_3AB79783126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE remote_image ADD CONSTRAINT FK_3AB79783126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE inscription_style RENAME INDEX idx_87f614afea750e8 TO IDX_61F4EC27EA750E8');
        $this->addSql('ALTER TABLE inscription_style RENAME INDEX idx_87f614af6de44026 TO IDX_61F4EC276DE44026');
        $this->addSql('ALTER TABLE inscription_style RENAME INDEX idx_87f614afea750e86de44026 TO IDX_61F4EC27EA750E86DE44026');
        $this->addSql('ALTER TABLE inscription_style RENAME INDEX uniq_87f614af5e237e06 TO UNIQ_61F4EC275E237E06');
        $this->addSql('ALTER TABLE item RENAME INDEX idx_1f1b251ece155f96 TO IDX_1F1B251E13581FD1');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE remote_image');
        $this->addSql('ALTER TABLE inscription_style RENAME INDEX idx_61f4ec276de44026 TO IDX_87F614AF6DE44026');
        $this->addSql('ALTER TABLE inscription_style RENAME INDEX idx_61f4ec27ea750e8 TO IDX_87F614AFEA750E8');
        $this->addSql('ALTER TABLE inscription_style RENAME INDEX idx_61f4ec27ea750e86de44026 TO IDX_87F614AFEA750E86DE44026');
        $this->addSql('ALTER TABLE inscription_style RENAME INDEX uniq_61f4ec275e237e06 TO UNIQ_87F614AF5E237E06');
        $this->addSql('ALTER TABLE item RENAME INDEX idx_1f1b251e13581fd1 TO IDX_1F1B251ECE155F96');
    }
}
