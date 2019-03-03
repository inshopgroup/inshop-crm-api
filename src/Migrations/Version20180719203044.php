<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180719203044 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO project_type (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (1, 'Internal', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO project_type (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (2, 'External', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO project_type (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (3, 'Other', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("select setval('project_type_id_seq', (select max(id) from project_type));");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
