<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231016201813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F126F525E');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E776C9F8');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EDF54B74F');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E8E893A58');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E13581FD1');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EC24AFBDB');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E776C9F8 FOREIGN KEY (period_start_id) REFERENCES period (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EDF54B74F FOREIGN KEY (period_end_id) REFERENCES period (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E8E893A58 FOREIGN KEY (findspot_id) REFERENCES location (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E13581FD1 FOREIGN KEY (inscription_style_id) REFERENCES inscription_style (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EC24AFBDB FOREIGN KEY (provenance_id) REFERENCES location (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE remote_image DROP FOREIGN KEY FK_3AB79783126F525E');
        $this->addSql('ALTER TABLE remote_image ADD CONSTRAINT FK_3AB79783126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F126F525E');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E776C9F8');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EDF54B74F');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E13581FD1');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E8E893A58');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EC24AFBDB');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E776C9F8 FOREIGN KEY (period_start_id) REFERENCES period (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EDF54B74F FOREIGN KEY (period_end_id) REFERENCES period (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E13581FD1 FOREIGN KEY (inscription_style_id) REFERENCES inscription_style (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E8E893A58 FOREIGN KEY (findspot_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EC24AFBDB FOREIGN KEY (provenance_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE remote_image DROP FOREIGN KEY FK_3AB79783126F525E');
        $this->addSql('ALTER TABLE remote_image ADD CONSTRAINT FK_3AB79783126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
    }
}
