<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170726134439 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE coupon_campaign (id INT AUTO_INCREMENT NOT NULL, campaign_name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coupon ADD coupon_campaign_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coupon ADD CONSTRAINT FK_64BF3F02815F64D3 FOREIGN KEY (coupon_campaign_id) REFERENCES coupon_campaign (id)');
        $this->addSql('CREATE INDEX IDX_64BF3F02815F64D3 ON coupon (coupon_campaign_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coupon DROP FOREIGN KEY FK_64BF3F02815F64D3');
        $this->addSql('DROP TABLE coupon_campaign');
        $this->addSql('DROP INDEX IDX_64BF3F02815F64D3 ON coupon');
        $this->addSql('ALTER TABLE coupon DROP coupon_campaign_id');
    }
}
