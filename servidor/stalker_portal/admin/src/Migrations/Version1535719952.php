<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719952 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE IF NOT EXISTS `storages_failure`(
    `id` int NOT NULL auto_increment,
    `storage_id` int NOT NULL default 0,
    `description` text,
    `added` timestamp not null,
    PRIMARY KEY (`id`),
    INDEX storage(`storage_id`, `added`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $schema->dropTable('storages_failure');
    }
}
