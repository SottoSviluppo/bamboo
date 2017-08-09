<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170619133918 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE entity_translation_old');
        $this->addSql('ALTER TABLE customer CHANGE facebook_id facebook_id VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE entity_translation_old (entity_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, entity_id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, entity_field VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, locale VARCHAR(8) NOT NULL COLLATE utf8_unicode_ci, translation LONGTEXT NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(entity_type, entity_id, entity_field, locale)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer CHANGE facebook_id facebook_id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
