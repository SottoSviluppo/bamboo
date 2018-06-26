<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180626091752 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE attachment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, content_type VARCHAR(255) NOT NULL, extension VARCHAR(10) NOT NULL, size INT NOT NULL, filename VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchasable_attachment (purchasable_id INT NOT NULL, attachment_id INT NOT NULL, INDEX IDX_1A1D4E839778C508 (purchasable_id), INDEX IDX_1A1D4E83464E68B (attachment_id), PRIMARY KEY(purchasable_id, attachment_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_attachment (category_id INT NOT NULL, attachment_id INT NOT NULL, INDEX IDX_5F4189C512469DE2 (category_id), INDEX IDX_5F4189C5464E68B (attachment_id), PRIMARY KEY(category_id, attachment_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchasable_attachment ADD CONSTRAINT FK_1A1D4E839778C508 FOREIGN KEY (purchasable_id) REFERENCES purchasable (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchasable_attachment ADD CONSTRAINT FK_1A1D4E83464E68B FOREIGN KEY (attachment_id) REFERENCES attachment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_attachment ADD CONSTRAINT FK_5F4189C512469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_attachment ADD CONSTRAINT FK_5F4189C5464E68B FOREIGN KEY (attachment_id) REFERENCES attachment (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE purchasable_attachment DROP FOREIGN KEY FK_1A1D4E83464E68B');
        $this->addSql('ALTER TABLE category_attachment DROP FOREIGN KEY FK_5F4189C5464E68B');
        $this->addSql('DROP TABLE attachment');
        $this->addSql('DROP TABLE purchasable_attachment');
        $this->addSql('DROP TABLE category_attachment');
    }
}
