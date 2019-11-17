<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720010 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE IF NOT EXISTS `dvb_channels`(
  `id` int NOT NULL auto_increment,
  `uid` int NOT NULL default 0,
  `channels` text,
  `modified` datetime,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`uid`)
) DEFAULT CHARSET=utf8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `dvb_channels`;
--
EOL
);
    }
}
