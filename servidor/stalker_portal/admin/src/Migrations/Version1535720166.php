<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720166 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE `themes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(64) NOT NULL DEFAULT '',
  `variables` TEXT,
  `added` TIMESTAMP NULL DEFAULT NULL,
  `updated` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE INDEX `alias` (`alias`),
  PRIMARY KEY (`id`)
) DEFAULT CHARSET = UTF8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `themes`;
--
EOL
);
    }
}
