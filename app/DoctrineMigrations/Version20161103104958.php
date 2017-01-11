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

        $this->addSql('CREATE TABLE permissions (id INT AUTO_INCREMENT NOT NULL, permission_group_id INT DEFAULT NULL, resource VARCHAR(255) NOT NULL, can_read TINYINT(1) NOT NULL, can_create TINYINT(1) NOT NULL, can_update TINYINT(1) NOT NULL, can_delete TINYINT(1) NOT NULL, INDEX IDX_2DEDCC6FB6C0CF1 (permission_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission_groups (id INT AUTO_INCREMENT NOT NULL, admin_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, INDEX IDX_39B79E9B642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE permissions ADD CONSTRAINT FK_2DEDCC6FB6C0CF1 FOREIGN KEY (permission_group_id) REFERENCES permission_groups (id)');
        $this->addSql('ALTER TABLE permission_groups ADD CONSTRAINT FK_39B79E9B642B8210 FOREIGN KEY (admin_id) REFERENCES admin_user (id) ON DELETE SET NULL');
        $this->addSql('DROP TABLE external_reference');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE permissions DROP FOREIGN KEY FK_2DEDCC6FB6C0CF1');
        $this->addSql('DROP TABLE permissions');
        $this->addSql('DROP TABLE permission_groups');
    }
}
