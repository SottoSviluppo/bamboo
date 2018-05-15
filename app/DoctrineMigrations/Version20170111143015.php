<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170111143015 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category_image (category_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_2D0E4B1612469DE2 (category_id), INDEX IDX_2D0E4B163DA5256D (image_id), PRIMARY KEY(category_id, image_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_icons (category_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_E2591F3712469DE2 (category_id), INDEX IDX_E2591F373DA5256D (image_id), PRIMARY KEY(category_id, image_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_image ADD CONSTRAINT FK_2D0E4B1612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_image ADD CONSTRAINT FK_2D0E4B163DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_icons ADD CONSTRAINT FK_E2591F3712469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_icons ADD CONSTRAINT FK_E2591F373DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coupon ADD color VARCHAR(255) DEFAULT NULL, ADD extra_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE country DROP enabled');
        $this->addSql('ALTER TABLE customer ADD country_id INT DEFAULT NULL, ADD extra_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('CREATE INDEX IDX_81398E09F92F3E70 ON customer (country_id)');
        $this->addSql('ALTER TABLE purchasable ADD barcode VARCHAR(255) DEFAULT NULL, ADD keep_cart_price TINYINT(1) DEFAULT NULL, ADD user_customizable TINYINT(1) DEFAULT NULL, ADD extra_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE category ADD principal_image_id INT DEFAULT NULL, ADD extra_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1A7F1F96B FOREIGN KEY (principal_image_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_64C19C1A7F1F96B ON category (principal_image_id)');
        $this->addSql('ALTER TABLE shipping_range CHANGE to_zone_id to_zone_id INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE category_image');
        $this->addSql('DROP TABLE category_icons');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1A7F1F96B');
        $this->addSql('DROP INDEX IDX_64C19C1A7F1F96B ON category');
        $this->addSql('ALTER TABLE category DROP principal_image_id, DROP extra_data');
        $this->addSql('ALTER TABLE country ADD enabled TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE coupon DROP color, DROP extra_data');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09F92F3E70');
        $this->addSql('DROP INDEX IDX_81398E09F92F3E70 ON customer');
        $this->addSql('ALTER TABLE customer DROP country_id, DROP extra_data');
        $this->addSql('ALTER TABLE purchasable DROP barcode, DROP keep_cart_price, DROP user_customizable, DROP extra_data');
        $this->addSql('ALTER TABLE shipping_range CHANGE to_zone_id to_zone_id INT NOT NULL');
    }
}
