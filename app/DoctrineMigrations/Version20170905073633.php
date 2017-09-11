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

        $this->addSql('DROP TABLE entity_translation_old');
        $this->addSql('ALTER TABLE cart ADD tax_currency_iso VARCHAR(3) DEFAULT NULL, ADD tax_amount INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B723E244C2 FOREIGN KEY (tax_currency_iso) REFERENCES currency (iso)');
        $this->addSql('CREATE INDEX IDX_BA388B723E244C2 ON cart (tax_currency_iso)');
        $this->addSql('ALTER TABLE fcn_documentheader ADD CONSTRAINT FK_3151A0791E37C62B FOREIGN KEY (document_currency_iso) REFERENCES currency (iso)');
        $this->addSql('ALTER TABLE fcn_documentheader ADD CONSTRAINT FK_3151A079B2A824D8 FOREIGN KEY (tax_id) REFERENCES tax (id)');
        $this->addSql('CREATE INDEX IDX_3151A0791E37C62B ON fcn_documentheader (document_currency_iso)');
        $this->addSql('CREATE INDEX IDX_3151A079B2A824D8 ON fcn_documentheader (tax_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE entity_translation_old (entity_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, entity_id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, entity_field VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, locale VARCHAR(8) NOT NULL COLLATE utf8_unicode_ci, translation LONGTEXT NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(entity_type, entity_id, entity_field, locale)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B723E244C2');
        $this->addSql('DROP INDEX IDX_BA388B723E244C2 ON cart');
        $this->addSql('ALTER TABLE cart DROP tax_currency_iso, DROP tax_amount');
        $this->addSql('ALTER TABLE fcn_documentheader DROP FOREIGN KEY FK_3151A0791E37C62B');
        $this->addSql('ALTER TABLE fcn_documentheader DROP FOREIGN KEY FK_3151A079B2A824D8');
        $this->addSql('DROP INDEX IDX_3151A0791E37C62B ON fcn_documentheader');
        $this->addSql('DROP INDEX IDX_3151A079B2A824D8 ON fcn_documentheader');
    }
}
