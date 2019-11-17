<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719949 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('storages')->hasColumn('not_for_mag100')) {
            $this->addSql('ALTER TABLE `storages` ADD `not_for_mag100` tinyint default 0;');
        }
        if (!$schema->getTable('events')->hasColumn('auto_hide_timeout')) {
            $this->addSql('ALTER TABLE `events` ADD `auto_hide_timeout` int NOT NULL default 0;');
        }
        if (!$schema->getTable('users')->hasColumn('serial_number')) {
            $this->addSql("ALTER TABLE `users` ADD `serial_number` varchar(32) NOT NULL default '';");
        }
        if (!$schema->getTable('users')->hasColumn('stb_type')) {
            $this->addSql("ALTER TABLE `users` ADD `stb_type` varchar(32) NOT NULL default '';");
        }
        $this->addSql(<<<EOL
--
CREATE TABLE IF NOT EXISTS `censored_channels`(
    `id` int NOT NULL auto_increment,
    `uid` int NOT NULL default 0,
    `list` text,
    `exclude` text,
    `changed` timestamp NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users` DROP `stb_type`;
ALTER TABLE `users` DROP `serial_number`;
ALTER TABLE `events` DROP `auto_hide_timeout`;
ALTER TABLE `storages` DROP `not_for_mag100`;
DROP TABLE IF EXISTS `censored_channels`;
--
EOL
);
    }
}
