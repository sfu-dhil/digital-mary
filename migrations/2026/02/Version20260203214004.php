<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203214004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Fix auth_user table columns

        // id
        $this->addSql('UPDATE auth_user SET `password` = ""');
        $this->addSql('ALTER TABLE auth_user CHANGE `login` `last_login` datetime');
        $this->addSql('ALTER TABLE auth_user ADD is_superuser tinyint(1) DEFAULT False');
        $this->addSql('UPDATE auth_user SET `is_superuser` = roles like \'%"ROLE_ADMIN"%\'');
        $this->addSql('ALTER TABLE auth_user ADD username varchar(150) DEFAULT ""');
        $this->addSql('UPDATE auth_user SET `username` = email');
        $this->addSql('ALTER TABLE auth_user ADD first_name varchar(150) DEFAULT ""');
        $this->addSql('ALTER TABLE auth_user ADD last_name varchar(150) DEFAULT ""');
        $this->addSql('UPDATE auth_user SET `first_name` = REGEXP_REPLACE(fullname, " .*$", "")');
        $this->addSql('UPDATE auth_user SET `last_name` = REGEXP_REPLACE(fullname, "^.* ", "")');
        // email
        $this->addSql('ALTER TABLE auth_user ADD is_staff tinyint(1) DEFAULT True');
        $this->addSql('ALTER TABLE auth_user CHANGE `active` `is_active` tinyint(1)');
        $this->addSql('ALTER TABLE auth_user CHANGE `created` `date_joined` datetime');

        $this->addSql('ALTER TABLE auth_user DROP reset_token');
        $this->addSql('ALTER TABLE auth_user DROP reset_expiry');
        $this->addSql('ALTER TABLE auth_user DROP fullname');
        $this->addSql('ALTER TABLE auth_user DROP roles');
        $this->addSql('ALTER TABLE auth_user DROP updated');
        $this->addSql('ALTER TABLE auth_user DROP affiliation');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}