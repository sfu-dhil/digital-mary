<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203214003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Fix digital_mary_image table columns

        // id
        $this->addSql('ALTER TABLE digital_mary_image CHANGE `original_name` `name` varchar(255)');
        $this->addSql('ALTER TABLE digital_mary_image CHANGE `public` `is_public` tinyint(1)');
        $this->addSql('ALTER TABLE digital_mary_image CHANGE `image_path` `image` varchar(100)');
        $this->addSql("UPDATE digital_mary_image SET `image` = CONCAT('images/', `image`)");
        // image_width
        // image_height
        $this->addSql('ALTER TABLE digital_mary_image CHANGE `thumb_path` `thumbnail` varchar(100)');
        $this->addSql("UPDATE digital_mary_image SET `thumbnail` = CONCAT('thumbnails/', `thumbnail`)");
        // description
        // license
        // created
        // updated
        // item_id

        $this->addSql('ALTER TABLE digital_mary_image DROP `image_size`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}