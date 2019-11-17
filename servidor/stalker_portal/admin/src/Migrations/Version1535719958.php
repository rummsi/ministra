<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719958 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('users')->hasColumn('tariff_plan_id')) {
            $this->addSql('ALTER TABLE `users` ADD `tariff_plan_id` int not null default 0;');
        }
        if (!$schema->getTable('access_tokens')->hasColumn('refresh_token')) {
            $this->addSql("ALTER TABLE `access_tokens` ADD `refresh_token` varchar(128) not null default '';");
        }
        $this->addSql(<<<EOL
--

CREATE TABLE IF NOT EXISTS `tariff_plan`(
  `id` int NOT NULL auto_increment,
  `external_id` varchar(64) not null default '',
  `name` varchar(64) not null default '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `services_package`(
  `id` int NOT NULL auto_increment,
  `external_id` varchar(64) not null default '',
  `name` varchar(64) not null default '',
  `description` text,
  `type` varchar(64) not null default '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `package_in_plan`(
  `id` int NOT NULL auto_increment,
  `package_id` int NOT NULL DEFAULT 0,
  `plan_id` int NOT NULL DEFAULT 0,
  `optional` tinyint default 0,
  `modified` timestamp not null,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `service_in_package`(
  `id` int NOT NULL auto_increment,
  `service_id` varchar(64) not null default '',
  `package_id` int NOT NULL DEFAULT 0,
  `type` varchar(64) not null default '',
  `modified` timestamp not null,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `user_package_subscription`(
  `id` int NOT NULL auto_increment,
  `user_id` int NOT NULL DEFAULT 0,
  `package_id` int NOT NULL DEFAULT 0,
  `modified` timestamp not null,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `access_tokens` DROP `refresh_token`;
ALTER TABLE `users` DROP `tariff_plan_id`;
DROP TABLE IF EXISTS `tariff_plan`;
DROP TABLE IF EXISTS `package_in_plan`;
DROP TABLE IF EXISTS `service_in_package`;
DROP TABLE IF EXISTS `services_package`;
DROP TABLE IF EXISTS `user_package_subscription`;
--
EOL
);
    }
}
