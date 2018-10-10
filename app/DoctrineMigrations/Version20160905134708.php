<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160905134708 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE social (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, class VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart_line ADD CONSTRAINT FK_3EF1B4CFB2A824D8 FOREIGN KEY (tax_id) REFERENCES tax (id)');
        $this->addSql('CREATE INDEX IDX_3EF1B4CFB2A824D8 ON cart_line (tax_id)');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE1B2A824D8 FOREIGN KEY (tax_id) REFERENCES tax (id)');
        $this->addSql('CREATE INDEX IDX_9CE58EE1B2A824D8 ON order_line (tax_id)');
        $this->addSql('ALTER TABLE address CHANGE country_id country_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('CREATE INDEX IDX_D4E6F81F92F3E70 ON address (country_id)');
        $this->addSql('ALTER TABLE country CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE store ADD CONSTRAINT FK_FF57587755F92F8B FOREIGN KEY (default_tax_id) REFERENCES tax (id)');
        $this->addSql('CREATE INDEX IDX_FF57587755F92F8B ON store (default_tax_id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09B2A824D8 FOREIGN KEY (tax_id) REFERENCES tax (id)');
        $this->addSql('CREATE INDEX IDX_81398E09B2A824D8 ON customer (tax_id)');
        $this->addSql('ALTER TABLE purchasable ADD CONSTRAINT FK_FC2E9BFEB2A824D8 FOREIGN KEY (tax_id) REFERENCES tax (id)');
        $this->addSql('CREATE INDEX IDX_FC2E9BFEB2A824D8 ON purchasable (tax_id)');
        $this->addSql('ALTER TABLE pack_purchasable DROP FOREIGN KEY FK_8D846271919B217');
        $this->addSql('ALTER TABLE pack_purchasable DROP FOREIGN KEY FK_8D846279778C508');
        $this->addSql('ALTER TABLE pack_purchasable ADD CONSTRAINT FK_8D846271919B217 FOREIGN KEY (pack_id) REFERENCES pack (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pack_purchasable ADD CONSTRAINT FK_8D846279778C508 FOREIGN KEY (purchasable_id) REFERENCES purchasable (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE social');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81F92F3E70');
        $this->addSql('DROP INDEX IDX_D4E6F81F92F3E70 ON address');
        $this->addSql('ALTER TABLE address CHANGE country_id country_id INT NOT NULL');
        $this->addSql('ALTER TABLE cart_line DROP FOREIGN KEY FK_3EF1B4CFB2A824D8');
        $this->addSql('DROP INDEX IDX_3EF1B4CFB2A824D8 ON cart_line');
        $this->addSql('ALTER TABLE country CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09B2A824D8');
        $this->addSql('DROP INDEX IDX_81398E09B2A824D8 ON customer');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE1B2A824D8');
        $this->addSql('DROP INDEX IDX_9CE58EE1B2A824D8 ON order_line');
        $this->addSql('ALTER TABLE pack_purchasable DROP FOREIGN KEY FK_8D846271919B217');
        $this->addSql('ALTER TABLE pack_purchasable DROP FOREIGN KEY FK_8D846279778C508');
        $this->addSql('ALTER TABLE pack_purchasable ADD CONSTRAINT FK_8D846271919B217 FOREIGN KEY (pack_id) REFERENCES pack (id)');
        $this->addSql('ALTER TABLE pack_purchasable ADD CONSTRAINT FK_8D846279778C508 FOREIGN KEY (purchasable_id) REFERENCES purchasable (id)');
        $this->addSql('ALTER TABLE purchasable DROP FOREIGN KEY FK_FC2E9BFEB2A824D8');
        $this->addSql('DROP INDEX IDX_FC2E9BFEB2A824D8 ON purchasable');
        $this->addSql('ALTER TABLE store DROP FOREIGN KEY FK_FF57587755F92F8B');
        $this->addSql('DROP INDEX IDX_FF57587755F92F8B ON store');
    }
}
