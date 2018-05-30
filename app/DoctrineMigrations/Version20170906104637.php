<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170906104637 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE custom_price_helper (id INT AUTO_INCREMENT NOT NULL, purchasable_id INT NOT NULL, customer_id INT DEFAULT NULL, price INT DEFAULT NULL, reduced_price INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B129C4FB9778C508 (purchasable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE custom_price_helper ADD CONSTRAINT FK_B129C4FB9778C508 FOREIGN KEY (purchasable_id) REFERENCES purchasable (id)');
        $this->addSql('ALTER TABLE category CHANGE show_in_home show_in_home TINYINT(1) DEFAULT \'0\' NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE custom_price_helper');
        $this->addSql('ALTER TABLE category CHANGE show_in_home show_in_home TINYINT(1) NOT NULL');
    }
}
