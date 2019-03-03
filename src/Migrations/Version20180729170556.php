<?php declare(strict_types=1);

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
        $this->addSql("INSERT INTO contact_type (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (1, 'Phone', '2018-06-16 11:56:28', '2018-06-16 11:56:28', null, null, null);");
        $this->addSql("INSERT INTO contact_type (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (2, 'Mobile', '2018-06-16 11:56:39', '2018-06-16 11:56:39', null, null, null);");
        $this->addSql("INSERT INTO contact_type (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (3, 'Fax', '2018-06-16 11:56:39', '2018-06-16 11:56:39', null, null, null);");
        $this->addSql("INSERT INTO contact_type (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (4, 'Email', '2018-06-16 11:56:39', '2018-06-16 11:56:39', null, null, null);");
        $this->addSql("INSERT INTO contact_type (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (5, 'WWW', '2018-06-16 11:56:28', '2018-06-16 11:56:28', null, null, null);");
        $this->addSql("select setval('contact_type_id_seq', (select max(id) from contact_type));");

        $this->addSql("INSERT INTO invoice_status (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (1, 'New', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO invoice_status (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (2, 'PDF generated', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO invoice_status (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (3, 'Sent', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO invoice_status (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (4, 'Paid', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("select setval('invoice_status_id_seq', (select max(id) from invoice_status));");

        $this->addSql("INSERT INTO invoice_type (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (1, 'General', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("select setval('invoice_type_id_seq', (select max(id) from invoice_type));");

        $this->addSql("INSERT INTO language (id, name, code, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (1, 'English', 'en', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO language (id, name, code, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (2, 'German', 'de', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO language (id, name, code, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (3, 'Polish', 'pl', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO language (id, name, code, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (4, 'Russian', 'ru', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO language (id, name, code, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (5, 'Spanish', 'es', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO language (id, name, code, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (6, 'French', 'fr', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO language (id, name, code, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (7, 'Italian', 'it', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("select setval('language_id_seq', (select max(id) from language));");

        $this->addSql("INSERT INTO order_line_status (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (1, 'New', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO order_line_status (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (2, 'Confirmed', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO order_line_status (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (3, 'Cancelled', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("select setval('order_line_status_id_seq', (select max(id) from order_line_status));");

        $this->addSql("INSERT INTO order_status (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (1, 'New', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO order_status (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (2, 'Confirmed', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO order_status (id, name, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (3, 'Cancelled', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("select setval('order_status_id_seq', (select max(id) from order_status));");

        $this->addSql("INSERT INTO vat (id, name, value, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (1, '23%', 23, '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO vat (id, name, value, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (2, '8%', 8, '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO vat (id, name, value, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (3, '5%', 5, '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO vat (id, name, value, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (4, 'None', 0, '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("select setval('vat_id_seq', (select max(id) from vat));");

        $this->addSql("INSERT INTO currency (id, name, code, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (1, 'Dollar USA', 'USD', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO currency (id, name, code, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (2, 'Euro', 'EUR', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("INSERT INTO currency (id, name, code, created_at, updated_at, created_by, updated_by, deleted_at) VALUES (3, 'Polish zloty', 'PLN', '2018-07-19 20:31:09', '2018-07-19 20:31:12', 'admin', null, null);");
        $this->addSql("select setval('currency_id_seq', (select max(id) from currency));");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
