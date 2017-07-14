<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170713124824 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE customer_coupon (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, coupon_id INT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D57AE3539395C3F3 (customer_id), INDEX IDX_D57AE35366C5951B (coupon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_coupon ADD CONSTRAINT FK_D57AE3539395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE customer_coupon ADD CONSTRAINT FK_D57AE35366C5951B FOREIGN KEY (coupon_id) REFERENCES coupon (id)');
        $this->addSql('DROP TABLE sdc_customer_coupon');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sdc_customer_coupon (id INT AUTO_INCREMENT NOT NULL, coupon_id INT NOT NULL, customer_id INT NOT NULL, INDEX IDX_8996B2C69395C3F3 (customer_id), INDEX IDX_8996B2C666C5951B (coupon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sdc_customer_coupon ADD CONSTRAINT FK_8996B2C666C5951B FOREIGN KEY (coupon_id) REFERENCES coupon (id)');
        $this->addSql('ALTER TABLE sdc_customer_coupon ADD CONSTRAINT FK_8996B2C69395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('DROP TABLE customer_coupon');
    }
}
