<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720032 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('itv')->hasColumn('locked')) {
            $this->addSql('ALTER TABLE `itv` ADD COLUMN `locked` TINYINT NOT NULL DEFAULT 0;');
        }
        $this->addSql(<<<EOL
--
CREATE TABLE IF NOT EXISTS `admin_dropdown_attributes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `admin_id` INT NOT NULL,
  `controller_name` VARCHAR(100) NOT NULL,
  `action_name` VARCHAR(100) NOT NULL,
  `dropdown_attributes` text,
  PRIMARY KEY (`id`));

EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE IF EXISTS `admin_dropdown_attributes`;
ALTER TABLE `itv` DROP `locked`;
--
EOL
);
    }
}
