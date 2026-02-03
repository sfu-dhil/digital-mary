<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203214002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // drop mines blog and feedback tables (no content yet)
        $tables = [
            'nines_blog_page',
            'nines_blog_post',
            'nines_blog_post_category',
            'nines_blog_post_status',
            'nines_feedback_comment_note',
            'nines_feedback_comment',
            'nines_feedback_comment_status',
        ];
        foreach ($tables as $tableName) {
            $this->addSql("
                DROP TABLE $tableName");
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}