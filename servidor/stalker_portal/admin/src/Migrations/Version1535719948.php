<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719948 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('users')->hasColumn('password')) {
            $this->addSql("ALTER TABLE `users` ADD `password` varchar(64) NOT NULL default '';");
        }
        if (!$schema->getTable('users')->hasColumn('login')) {
            $this->addSql("ALTER TABLE `users` ADD `login` varchar(64) NOT NULL default '';");
        }
        $this->addSql(<<<EOL
--
ALTER TABLE `users` DROP INDEX `mac`;
ALTER TABLE `users` ADD KEY `mac` (`mac`);
CREATE TABLE IF NOT EXISTS `user_modules`(
    `id` int NOT NULL auto_increment,
    `uid` int NOT NULL default 0,
    `restricted` text,
    `disabled` text,
    `changed` timestamp NOT NULL,
    UNIQUE KEY (`uid`),
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `video_on_tasks`(
    `id` int NOT NULL auto_increment,
    `video_id` int NOT NULL default 0,
    `date_on`  date,
    `added` timestamp NOT NULL,
    UNIQUE KEY (`video_id`),
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users` DROP `login`;
ALTER TABLE `users` DROP `password`;
DROP TABLE IF EXISTS `user_modules`;
DROP TABLE IF EXISTS `video_on_tasks`;
--
EOL
);
    }
}
