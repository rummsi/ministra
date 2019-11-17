<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720083 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE `api_storage` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL DEFAULT 0,
  `key` VARCHAR(128) NULL DEFAULT '',
  `value` TEXT,
  `added` DATETIME NOT NULL,
  `updated` DATETIME NOT NULL,
  `last_access` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET = UTF8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `api_storage`;
--
EOL
);
    }
}
