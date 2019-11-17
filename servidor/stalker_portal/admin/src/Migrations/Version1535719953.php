<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719953 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('access_tokens')->hasColumn('started')) {
            $this->addSql('ALTER TABLE `access_tokens` ADD `started` timestamp null default null;');
        }
        if (!$schema->getTable('access_tokens')->hasColumn('time_delta')) {
            $this->addSql("ALTER TABLE `access_tokens` ADD `time_delta` varchar(128) NOT NULL default '';");
        }
        if (!$schema->getTable('access_tokens')->hasColumn('secret_key')) {
            $this->addSql("ALTER TABLE `access_tokens` ADD `secret_key` varchar(128) NOT NULL default '';");
        }
        $this->addSql(<<<EOL
--

CREATE TABLE IF NOT EXISTS `developer_api_key`(
    `id` int NOT NULL auto_increment,
    `uid` int NOT NULL default 0,
    `api_key` varchar(128) NOT NULL default '',
    `comment` text not null,
    `expires` timestamp null default null,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`api_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `access_tokens` DROP `secret_key`;
ALTER TABLE `access_tokens` DROP `time_delta`;
ALTER TABLE `access_tokens` DROP `started`;
DROP TABLE IF EXISTS `developer_api_key`;
--
EOL
);
    }
}
