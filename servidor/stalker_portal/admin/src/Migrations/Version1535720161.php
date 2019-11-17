<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720161 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE `smac_codes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(32) NOT NULL DEFAULT '',
  `request` VARCHAR(128) NOT NULL DEFAULT '',
  `user_id` INT NOT NULL DEFAULT 0,
  `device` VARCHAR(64) NOT NULL DEFAULT '',
  `status` ENUM("Not Activated", "Activated", "Blocked") DEFAULT "Not Activated",
  `added` TIMESTAMP NULL DEFAULT NULL,
  `updated` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE INDEX `code` (`code`),
  PRIMARY KEY (`id`)
) DEFAULT CHARSET = UTF8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `smac_codes`;
--
EOL
);
    }
}
