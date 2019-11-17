<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720043 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE `download_links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `link_hash` char(32) NOT NULL DEFAULT '',
  `uid` int NOT NULL DEFAULT 0,
  `type` varchar(16) NOT NULL DEFAULT '',
  `media_id` int NOT NULL DEFAULT 0,
  `param1` varchar(32) NOT NULL DEFAULT '',
  `added` timestamp null default null,
  UNIQUE INDEX `link_hash` (`link_hash`),
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `download_links`;
--
EOL
);
    }
}
