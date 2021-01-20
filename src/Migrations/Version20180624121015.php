<?php declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180624121015 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO task_status (id, name, created_at, updated_at, created_by, updated_by, is_active) VALUES (1, 'To do', '2018-06-17 09:06:06', '2018-06-17 09:06:06', 'admin', 'admin', true);");
        $this->addSql("INSERT INTO task_status (id, name, created_at, updated_at, created_by, updated_by, is_active) VALUES (2, 'In progress', '2018-06-17 09:03:09', '2018-06-17 09:03:09', 'admin', 'admin', true);");
        $this->addSql("INSERT INTO task_status (id, name, created_at, updated_at, created_by, updated_by, is_active) VALUES (3, 'Done', '2018-06-17 09:02:45', '2018-06-17 09:02:45', 'admin', 'admin', true);");

        $this->addSql("INSERT INTO project_status (id, name, created_at, updated_at, created_by, updated_by, is_active) VALUES (1, 'Open', '2018-06-17 10:46:22', '2018-06-17 10:46:22', 'admin', 'admin', true);");
        $this->addSql("INSERT INTO project_status (id, name, created_at, updated_at, created_by, updated_by, is_active) VALUES (2, 'In Progress', '2018-06-17 09:17:39', '2018-06-17 09:17:39', null, null, true);");
        $this->addSql("INSERT INTO project_status (id, name, created_at, updated_at, created_by, updated_by, is_active) VALUES (3, 'Closed', '2018-06-17 10:26:45', '2018-06-17 10:27:48', null, null, true);");
    }

    public function down(Schema $schema) : void
    {
    }
}
