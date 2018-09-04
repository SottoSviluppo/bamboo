<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170322135321 extends AbstractMigration {
	/**
	 * @param Schema $schema
	 */
	public function up(Schema $schema) {
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

		$this->addSql('ALTER TABLE store CHANGE private_product private_product_creation TINYINT(1) DEFAULT NULL');
		//$this->addSql('ALTER TABLE shipping_range CHANGE country_id country_id INT NOT NULL');
	}

	/**
	 * @param Schema $schema
	 */
	public function down(Schema $schema) {
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

		$this->addSql('ALTER TABLE shipping_range CHANGE country_id country_id INT DEFAULT NULL');
		$this->addSql('ALTER TABLE store CHANGE private_product_creation private_product TINYINT(1) DEFAULT NULL');
	}
}
