<?php

declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210120111431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("select setval('task_status_id_seq', (select max(id) from task_status));");
        $this->addSql("select setval('project_status_id_seq', (select max(id) from project_status));");
        $this->addSql("select setval('project_type_id_seq', (select max(id) from project_type));");
        $this->addSql("select setval('contact_type_id_seq', (select max(id) from contact_type));");
        $this->addSql("select setval('language_id_seq', (select max(id) from language));");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
