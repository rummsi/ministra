<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719954 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('administrators')->hasColumn('debug_key')) {
            $this->addSql("ALTER TABLE `administrators` ADD `debug_key` varchar(128) NOT NULL default '';");
        }
        if (!$schema->getTable('itv')->hasColumn('logo')) {
            $this->addSql("ALTER TABLE `itv` ADD `logo` varchar(128) NOT NULL default '';");
        }
        $this->addSql(<<<EOL
--
UPDATE `administrators` SET `debug_key`=md5(rand()) WHERE `access`=0;
CREATE TABLE IF NOT EXISTS `user_downloads`(
    `id` int NOT NULL auto_increment,
    `uid` int NOT NULL default 0,
    `downloads` text,
    `modified` timestamp not null,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `itv` DROP `logo`;
ALTER TABLE `administrators` DROP `debug_key`;
DROP TABLE IF EXISTS `user_downloads`;
--
EOL
);
    }
}
