<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180530095733 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE coupon_category (coupon_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_1FCF47F366C5951B (coupon_id), INDEX IDX_1FCF47F312469DE2 (category_id), PRIMARY KEY(coupon_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customers_coupons (coupon_id INT NOT NULL, customer_category_id INT NOT NULL, INDEX IDX_C92CC2FD66C5951B (coupon_id), INDEX IDX_C92CC2FD110DB6EA (customer_category_id), PRIMARY KEY(coupon_id, customer_category_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customers_categories (customer_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_47C80B619395C3F3 (customer_id), INDEX IDX_47C80B6112469DE2 (category_id), PRIMARY KEY(customer_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, enabled TINYINT(1) NOT NULL, extra_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coupon_category ADD CONSTRAINT FK_1FCF47F366C5951B FOREIGN KEY (coupon_id) REFERENCES coupon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coupon_category ADD CONSTRAINT FK_1FCF47F312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customers_coupons ADD CONSTRAINT FK_C92CC2FD66C5951B FOREIGN KEY (coupon_id) REFERENCES coupon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customers_coupons ADD CONSTRAINT FK_C92CC2FD110DB6EA FOREIGN KEY (customer_category_id) REFERENCES customer_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customers_categories ADD CONSTRAINT FK_47C80B619395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customers_categories ADD CONSTRAINT FK_47C80B6112469DE2 FOREIGN KEY (category_id) REFERENCES customer_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coupon ADD include_categories INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customers_coupons DROP FOREIGN KEY FK_C92CC2FD110DB6EA');
        $this->addSql('ALTER TABLE customers_categories DROP FOREIGN KEY FK_47C80B6112469DE2');
        $this->addSql('DROP TABLE coupon_category');
        $this->addSql('DROP TABLE customers_coupons');
        $this->addSql('DROP TABLE customers_categories');
        $this->addSql('DROP TABLE customer_category');
        $this->addSql('ALTER TABLE coupon DROP include_categories');
    }
}
