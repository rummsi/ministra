<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719940 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
ALTER TABLE `itv` ADD `enable_tv_archive` tinyint default 0;
CREATE TABLE IF NOT EXISTS `tv_archive`(
    `id` int NOT NULL auto_increment,
    `ch_id` int NOT NULL default 0,
    `storage_name` varchar(128) NOT NULL default '',
    `start_time` timestamp null default null,
    `end_time` timestamp null default null,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`ch_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `itv` DROP `enable_tv_archive`;
DROP TABLE IF EXISTS `tv_archive`;
--
EOL
);
    }
}
