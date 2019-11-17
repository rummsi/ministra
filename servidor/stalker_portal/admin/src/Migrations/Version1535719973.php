<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719973 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('image_update_settings')->hasColumn('stb_type')) {
            $this->addSql("ALTER TABLE `image_update_settings` ADD `stb_type` VARCHAR(64) NOT NULL DEFAULT '';");
        }
        if (!$schema->getTable('services_package')->hasColumn('rent_duration')) {
            $this->addSql('ALTER TABLE `services_package` ADD `rent_duration` int NOT NULL default 0;');
        }
        $this->addSql(<<<EOL
--

CREATE TABLE IF NOT EXISTS `video_rent`(
  `id` int NOT NULL auto_increment,
  `uid` int NOT NULL default 0,
  `video_id` int NOT NULL default 0,
  `price` varchar(32) NOT NULL default '',
  `rent_history_id` int NOT NULL default 0,
  `rent_date` timestamp null default null,
  `rent_end_date` timestamp null default null,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `video_rent_history`(
  `id` int NOT NULL auto_increment,
  `uid` int NOT NULL default 0,
  `video_id` int NOT NULL default 0,
  `price` varchar(32) NOT NULL default '',
  `rent_date` timestamp null default null,
  `rent_end_date` timestamp null default null,
  `start_watching_date` timestamp null default null,
  `watched` tinyint default 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `services_package` DROP `rent_duration`;
DROP TABLE `video_rent`;
DROP TABLE `video_rent_history`;
ALTER TABLE `image_update_settings` DROP `stb_type`;
--
EOL
);
    }
}
