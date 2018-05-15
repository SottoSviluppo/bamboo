<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170905073633 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cart ADD tax_currency_iso VARCHAR(3) DEFAULT NULL, ADD tax_amount INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B723E244C2 FOREIGN KEY (tax_currency_iso) REFERENCES currency (iso)');
        $this->addSql('CREATE INDEX IDX_BA388B723E244C2 ON cart (tax_currency_iso)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B723E244C2');
        $this->addSql('DROP INDEX IDX_BA388B723E244C2 ON cart');
        $this->addSql('ALTER TABLE cart DROP tax_currency_iso, DROP tax_amount');
    }
}
