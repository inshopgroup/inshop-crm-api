<?php

declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180729170556 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO contact_type (id, name, created_at, updated_at, created_by, updated_by, is_active) VALUES (1, 'Phone', '2018-06-16 11:56:28', '2018-06-16 11:56:28', null, null, true);");
        $this->addSql("INSERT INTO contact_type (id, name, created_at, updated_at, created_by, updated_by, is_active) VALUES (2, 'Mobile', '2018-06-16 11:56:39', '2018-06-16 11:56:39', null, null, true);");
        $this->addSql("INSERT INTO contact_type (id, name, created_at, updated_at, created_by, updated_by, is_active) VALUES (3, 'Fax', '2018-06-16 11:56:39', '2018-06-16 11:56:39', null, null, true);");
        $this->addSql("INSERT INTO contact_type (id, name, created_at, updated_at, created_by, updated_by, is_active) VALUES (4, 'Email', '2018-06-16 11:56:39', '2018-06-16 11:56:39', null, null, true);");
        $this->addSql("INSERT INTO contact_type (id, name, created_at, updated_at, created_by, updated_by, is_active) VALUES (5, 'WWW', '2018-06-16 11:56:28', '2018-06-16 11:56:28', null, null, true);");

        $this->addSql("INSERT INTO language (id, name, code, created_at, updated_at, created_by, updated_by, is_active) VALUES (1, 'English', 'en', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, true);");
        $this->addSql("INSERT INTO language (id, name, code, created_at, updated_at, created_by, updated_by, is_active) VALUES (2, 'German', 'de', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, true);");
        $this->addSql("INSERT INTO language (id, name, code, created_at, updated_at, created_by, updated_by, is_active) VALUES (3, 'Polish', 'pl', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, true);");
        $this->addSql("INSERT INTO language (id, name, code, created_at, updated_at, created_by, updated_by, is_active) VALUES (4, 'Ukrainian', 'ua', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, true);");
        $this->addSql("INSERT INTO language (id, name, code, created_at, updated_at, created_by, updated_by, is_active) VALUES (5, 'Spanish', 'es', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, true);");
        $this->addSql("INSERT INTO language (id, name, code, created_at, updated_at, created_by, updated_by, is_active) VALUES (6, 'French', 'fr', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, true);");
        $this->addSql("INSERT INTO language (id, name, code, created_at, updated_at, created_by, updated_by, is_active) VALUES (7, 'Italian', 'it', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, true);");
   }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
