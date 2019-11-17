<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720122 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE IF NOT EXISTS `diagnostic_info` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `uid` INT NOT NULL DEFAULT 0,
  `changed` TIMESTAMP NULL DEFAULT NULL,
  `info` TEXT,
  INDEX `uid` (`uid`),
  PRIMARY KEY (`id`)
) DEFAULT CHARSET = UTF8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `diagnostic_info`;
--
EOL
);
    }
}
