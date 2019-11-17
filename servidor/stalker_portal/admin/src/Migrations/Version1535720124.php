<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720124 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE `notification_feed` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(128) NOT NULL DEFAULT '',
  `description` text,
  `link` VARCHAR(128) NOT NULL DEFAULT '',
  `category` VARCHAR(32) NOT NULL DEFAULT '',
  `pub_date` TIMESTAMP NULL DEFAULT NULL,
  `guid` VARCHAR(32) NOT NULL DEFAULT '',
  `read` TINYINT NOT NULL DEFAULT 0,
  `delay_finished_time` TIMESTAMP NULL DEFAULT NULL,
  `added` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET = UTF8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `notification_feed`;
--
EOL
);
    }
}
