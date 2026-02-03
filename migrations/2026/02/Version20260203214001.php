<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203214001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // fix location/subject alternate_names array column values
        $tables = [
            'digital_mary_location',
            'digital_mary_subject',
        ];
        foreach ($tables as $tableName) {
            $this->addSql("
                UPDATE $tableName
                SET alternate_names = REGEXP_REPLACE(
                    REGEXP_REPLACE(
                        REGEXP_REPLACE(
                            alternate_names, '^a:[0-9]+:|i:[0-9]+;s:[0-9]+:', ''
                        ),
                        '\";}', '\"}'
                    ),
                    '\";\"', '\",\"'
                )
            ");
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}