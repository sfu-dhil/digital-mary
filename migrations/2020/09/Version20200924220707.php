<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200924220707 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item_language (item_id INT NOT NULL, language_id INT NOT NULL, INDEX IDX_B8D6B97E126F525E (item_id), INDEX IDX_B8D6B97E82F1BAF4 (language_id), PRIMARY KEY(item_id, language_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_civilization (item_id INT NOT NULL, civilization_id INT NOT NULL, INDEX IDX_B7F8DF00126F525E (item_id), INDEX IDX_B7F8DF006946BDDB (civilization_id), PRIMARY KEY(item_id, civilization_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_category (item_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_6A41D10A126F525E (item_id), INDEX IDX_6A41D10A12469DE2 (category_id), PRIMARY KEY(item_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE item_language ADD CONSTRAINT FK_B8D6B97E126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_language ADD CONSTRAINT FK_B8D6B97E82F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id) ON DELETE CASCADE');

        $this->addSql('INSERT INTO item_language (item_id, language_id) SELECT id, inscription_language_id from item where inscription_language_id is not null');

        $this->addSql('ALTER TABLE item_civilization ADD CONSTRAINT FK_B7F8DF00126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_civilization ADD CONSTRAINT FK_B7F8DF006946BDDB FOREIGN KEY (civilization_id) REFERENCES civilization (id) ON DELETE CASCADE');

        $this->addSql('INSERT INTO item_civilization (item_id, civilization_id) SELECT id, civilization_id from item where civilization_id is not null');

        $this->addSql('ALTER TABLE item_category ADD CONSTRAINT FK_6A41D10A126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_category ADD CONSTRAINT FK_6A41D10A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');

        $this->addSql('INSERT INTO item_category (item_id, category_id) SELECT id, category_id from item where category_id is not null');

        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E12469DE2');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E6946BDDB');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E75802724');
        $this->addSql('DROP INDEX IDX_1F1B251E12469DE2 ON item');
        $this->addSql('DROP INDEX IDX_1F1B251E6946BDDB ON item');
        $this->addSql('DROP INDEX IDX_1F1B251E75802724 ON item');
        $this->addSql('ALTER TABLE item DROP category_id, DROP civilization_id, DROP inscription_language_id');
    }

    public function down(Schema $schema) : void
    {
        $this->throwIrreversibleMigrationException();
    }
}
