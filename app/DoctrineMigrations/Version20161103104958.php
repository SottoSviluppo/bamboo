<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161103104958 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE custom_price_range DROP FOREIGN KEY FK_8E72E7E333C62083');
        $this->addSql('ALTER TABLE custom_price DROP FOREIGN KEY FK_9C5DAB0DC96902C');
        $this->addSql('ALTER TABLE custom_price_list_customer DROP FOREIGN KEY FK_5854B3D4DC96902C');
        $this->addSql('ALTER TABLE sdc_order_row DROP FOREIGN KEY FK_835581563C797BEC');
        $this->addSql('CREATE TABLE permissions (id INT AUTO_INCREMENT NOT NULL, permission_group_id INT DEFAULT NULL, resource VARCHAR(255) NOT NULL, can_read TINYINT(1) NOT NULL, can_create TINYINT(1) NOT NULL, can_update TINYINT(1) NOT NULL, can_delete TINYINT(1) NOT NULL, INDEX IDX_2DEDCC6FB6C0CF1 (permission_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission_groups (id INT AUTO_INCREMENT NOT NULL, admin_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, INDEX IDX_39B79E9B642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE permissions ADD CONSTRAINT FK_2DEDCC6FB6C0CF1 FOREIGN KEY (permission_group_id) REFERENCES permission_groups (id)');
        $this->addSql('ALTER TABLE permission_groups ADD CONSTRAINT FK_39B79E9B642B8210 FOREIGN KEY (admin_id) REFERENCES admin_user (id) ON DELETE SET NULL');
        $this->addSql('DROP TABLE custom_price');
        $this->addSql('DROP TABLE custom_price_list');
        $this->addSql('DROP TABLE custom_price_list_customer');
        $this->addSql('DROP TABLE custom_price_range');
        $this->addSql('DROP TABLE external_reference');
        $this->addSql('DROP TABLE sdc_open_item');
        $this->addSql('DROP TABLE sdc_order_head');
        $this->addSql('DROP TABLE sdc_order_row');
        $this->addSql('DROP TABLE sdc_punti_fiocco');
        $this->addSql('DROP TABLE sdc_purchase_history');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE permissions DROP FOREIGN KEY FK_2DEDCC6FB6C0CF1');
        $this->addSql('CREATE TABLE custom_price (id INT AUTO_INCREMENT NOT NULL, purchasable_id INT DEFAULT NULL, custom_price_list_id INT NOT NULL, default_to_base_price TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_9C5DAB0DC96902C (custom_price_list_id), INDEX IDX_9C5DAB09778C508 (purchasable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE custom_price_list (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE custom_price_list_customer (custom_price_list_id INT NOT NULL, customer_id INT NOT NULL, INDEX IDX_5854B3D4DC96902C (custom_price_list_id), INDEX IDX_5854B3D49395C3F3 (customer_id), PRIMARY KEY(custom_price_list_id, customer_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE custom_price_range (id INT AUTO_INCREMENT NOT NULL, custom_price_id INT NOT NULL, price_currency_iso VARCHAR(3) DEFAULT NULL COLLATE utf8_unicode_ci, reduced_price_currency_iso VARCHAR(3) DEFAULT NULL COLLATE utf8_unicode_ci, from_qty INT DEFAULT NULL, to_qty INT DEFAULT NULL, price_discount INT DEFAULT NULL, reduced_price_discount INT DEFAULT NULL, price INT DEFAULT NULL, reduced_price INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_8E72E7E333C62083 (custom_price_id), INDEX IDX_8E72E7E347018B47 (price_currency_iso), INDEX IDX_8E72E7E3EB35D9BE (reduced_price_currency_iso), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE external_reference (id INT AUTO_INCREMENT NOT NULL, entity_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, entity_id INT NOT NULL, external_reference VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX extref_elcodi_idx (entity_name, entity_id), UNIQUE INDEX extref_ext_idx (entity_name, external_reference), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sdc_open_item (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, amount_currency_iso VARCHAR(3) DEFAULT NULL COLLATE utf8_unicode_ci, document_type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, document_number VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, document_date DATE DEFAULT NULL, payment_type VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, document_expiration_date DATE DEFAULT NULL, amount INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E2D15CD69395C3F3 (customer_id), INDEX IDX_E2D15CD6D82D7CDD (amount_currency_iso), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sdc_order_head (id INT AUTO_INCREMENT NOT NULL, customer_sdc_code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, num_rows INT NOT NULL, processed TINYINT(1) NOT NULL, processed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sdc_order_row (id INT AUTO_INCREMENT NOT NULL, sdc_order_head_id INT NOT NULL, product_sdc_code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, quantity INT NOT NULL, unit_price INT NOT NULL, price INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_835581563C797BEC (sdc_order_head_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sdc_punti_fiocco (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, points INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_2FF0F0E39395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sdc_purchase_history (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, purchasable_id INT NOT NULL, sett_1 INT DEFAULT NULL, sett_2 INT DEFAULT NULL, sett_3 INT DEFAULT NULL, sett_4 INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_BC5931A89395C3F3 (customer_id), INDEX IDX_BC5931A89778C508 (purchasable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE custom_price ADD CONSTRAINT FK_9C5DAB09778C508 FOREIGN KEY (purchasable_id) REFERENCES purchasable (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE custom_price ADD CONSTRAINT FK_9C5DAB0DC96902C FOREIGN KEY (custom_price_list_id) REFERENCES custom_price_list (id)');
        $this->addSql('ALTER TABLE custom_price_list_customer ADD CONSTRAINT FK_5854B3D49395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE custom_price_list_customer ADD CONSTRAINT FK_5854B3D4DC96902C FOREIGN KEY (custom_price_list_id) REFERENCES custom_price_list (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE custom_price_range ADD CONSTRAINT FK_8E72E7E333C62083 FOREIGN KEY (custom_price_id) REFERENCES custom_price (id)');
        $this->addSql('ALTER TABLE custom_price_range ADD CONSTRAINT FK_8E72E7E347018B47 FOREIGN KEY (price_currency_iso) REFERENCES currency (iso)');
        $this->addSql('ALTER TABLE custom_price_range ADD CONSTRAINT FK_8E72E7E3EB35D9BE FOREIGN KEY (reduced_price_currency_iso) REFERENCES currency (iso)');
        $this->addSql('ALTER TABLE sdc_open_item ADD CONSTRAINT FK_E2D15CD69395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE sdc_open_item ADD CONSTRAINT FK_E2D15CD6D82D7CDD FOREIGN KEY (amount_currency_iso) REFERENCES currency (iso)');
        $this->addSql('ALTER TABLE sdc_order_row ADD CONSTRAINT FK_835581563C797BEC FOREIGN KEY (sdc_order_head_id) REFERENCES sdc_order_head (id)');
        $this->addSql('ALTER TABLE sdc_punti_fiocco ADD CONSTRAINT FK_2FF0F0E39395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE sdc_purchase_history ADD CONSTRAINT FK_BC5931A89395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE sdc_purchase_history ADD CONSTRAINT FK_BC5931A89778C508 FOREIGN KEY (purchasable_id) REFERENCES purchasable (id)');
        $this->addSql('DROP TABLE permissions');
        $this->addSql('DROP TABLE permission_groups');
    }
}
