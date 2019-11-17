<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719956 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
CREATE TABLE IF NOT EXISTS `media_favorites`(
    `id` int NOT NULL auto_increment,
    `uid` int NOT NULL default 0,
    `favorites` text,
    `modified` timestamp not null,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $schema->dropTable('media_favorites');
    }
}
