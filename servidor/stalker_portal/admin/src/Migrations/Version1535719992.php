<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719992 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('epg')->hasColumn('actor')) {
            $this->addSql('ALTER TABLE `epg` ADD `actor` text;');
        }
        if (!$schema->getTable('epg')->hasColumn('director')) {
            $this->addSql("ALTER TABLE `epg` ADD `director` varchar(128) NOT NULL default '';");
        }
        if (!$schema->getTable('epg')->hasColumn('category')) {
            $this->addSql("ALTER TABLE `epg` ADD `category` varchar(128) NOT NULL default '';");
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `epg` DROP `category`;
ALTER TABLE `epg` DROP `director`;
ALTER TABLE `epg` DROP `actor`;
--
EOL
);
    }
}
