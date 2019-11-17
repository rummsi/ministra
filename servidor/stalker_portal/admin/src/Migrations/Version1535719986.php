<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719986 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE IF NOT EXISTS `played_tv_archive`(
  `id` int NOT NULL auto_increment,
  `ch_id` int NOT NULL default 0,
  `uid` int NOT NULL default 0,
  `length` int NOT NULL default 0,
  `playtime` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
);
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `played_tv_archive`;
--
EOL
);
    }
}
