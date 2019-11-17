<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720048 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE `apps` (
  `id` int NOT NULL AUTO_INCREMENT,
  `url` varchar(128) NOT NULL DEFAULT '',
  `current_version` varchar(16) NOT NULL DEFAULT '',
  `status` TINYINT NOT NULL DEFAULT 0,  
  `options` TEXT,
  `added` timestamp null default null,
  `updated` timestamp null default null,
  UNIQUE INDEX `url` (`url`),
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
CREATE TABLE `apps_tos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tos_en` TEXT,
  `accepted` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
INSERT INTO apps_tos (`tos_en`) VALUE ('Terms of Use text');  
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `apps`;
DROP TABLE `apps_tos`;
--
EOL
);
    }
}
