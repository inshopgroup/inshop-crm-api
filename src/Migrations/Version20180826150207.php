<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180826150207 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO payment_type (id, name, created_at, updated_at, created_by, updated_by, is_active) VALUES (1, 'Paypal', '2018-08-26 16:01:41', '2018-08-26 16:01:49', null, null, true);");
        $this->addSql("select setval('payment_type_id_seq', (select max(id) from payment_type));");

        $this->addSql("INSERT INTO shipment_method (id, name, created_at, updated_at, created_by, updated_by, is_active) VALUES (1, 'FedEx', '2018-08-26 16:03:09', '2018-08-26 16:03:11', null, null, true);");
        $this->addSql("select setval('shipment_method_id_seq', (select max(id) from shipment_method));");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
