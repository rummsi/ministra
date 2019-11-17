<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719966 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('ch_links')->hasColumn('enable_monitoring')) {
            $this->addSql('ALTER TABLE `ch_links` ADD `enable_monitoring` tinyint default 0;');
        }
        if (!$schema->getTable('streaming_servers')->hasColumn('stream_zone')) {
            $this->addSql('ALTER TABLE `streaming_servers` ADD `stream_zone` int not null default 0;');
        }
        $this->addSql(<<<EOL
--
CREATE TABLE IF NOT EXISTS `stream_zones`(
    `id` int NOT NULL auto_increment,
    `name` varchar(128) not null default '',
    `default_zone` tinyint default 0,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `countries_in_zone`(
    `id` int NOT NULL auto_increment,
    `country_id` int not null default 0,
    `zone_id` int not null default 0,
    INDEX (`country_id`),
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
UPDATE `itv` SET monitoring_status=1;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `stream_zones`;
DROP TABLE `countries_in_zone`;
ALTER TABLE `streaming_servers` DROP `stream_zone`;
ALTER TABLE `ch_links` DROP `enable_monitoring`;
--
EOL
);
    }
}
