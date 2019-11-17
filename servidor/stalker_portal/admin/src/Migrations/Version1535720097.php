<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720097 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE `launcher_apps` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `alias` varchar(128) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL DEFAULT '',
  `type` ENUM('app', 'theme', 'plugin', 'core', 'osd', 'launcher', 'system', 'auth'),
  `category` ENUM('', 'media', 'apps', 'games', 'notification'),
  `current_version` varchar(16) NOT NULL DEFAULT '',
  `description` text,
  `author` VARCHAR(64),
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `is_unique` tinyint(4) NOT NULL DEFAULT 0,
  `url` varchar(128) NOT NULL DEFAULT '',
  `autoupdate` tinyint(4) NOT NULL DEFAULT 0,
  `config` text,
  `localization` text,
  `added` timestamp null default null,
  `updated` timestamp null default null,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET = UTF8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `launcher_apps`;
--
EOL
);
    }
}
