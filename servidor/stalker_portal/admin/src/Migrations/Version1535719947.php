<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719947 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('itv')->hasColumn('quality')) {
            $this->addSql("ALTER TABLE `itv` ADD `quality` varchar(16) default 'high';");
        }
        if (!$schema->getTable('stream_error')->hasColumn('event')) {
            $this->addSql('ALTER TABLE `stream_error` ADD `event` tinyint unsigned default 0;');
        }
        if (!$schema->getTable('itv')->hasColumn('cmd_3')) {
            $this->addSql("ALTER TABLE `itv` ADD `cmd_3` varchar(128) NOT NULL default '';");
        }
        if (!$schema->getTable('itv')->hasColumn('cmd_2')) {
            $this->addSql("ALTER TABLE `itv` ADD `cmd_2` varchar(128) NOT NULL default '';");
        }
        if (!$schema->getTable('itv')->hasColumn('cmd_1')) {
            $this->addSql("ALTER TABLE `itv` ADD `cmd_1` varchar(128) NOT NULL default '';");
        }
        if (!$schema->getTable('storages')->hasColumn('archive_stream_server')) {
            $this->addSql("ALTER TABLE `storages` ADD `archive_stream_server` varchar(128) NOT NULL default '';");
        }
        if (!$schema->getTable('storages')->hasColumn('wowza_server')) {
            $this->addSql('ALTER TABLE `storages` ADD `wowza_server` tinyint default 0;');
        }
        if (!$schema->getTable('users')->hasColumn('fname')) {
            $this->addSql("ALTER TABLE `users` ADD `fname` varchar(64) NOT NULL default '';");
        }
        $this->addSql(<<<EOL
--

ALTER TABLE `itv` DROP `quality`;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users` DROP `fname`;
ALTER TABLE `storages` DROP `wowza_server`;
ALTER TABLE `storages` DROP `archive_stream_server`;

ALTER TABLE `itv` DROP `cmd_1`;
ALTER TABLE `itv` DROP `cmd_2`;
ALTER TABLE `itv` DROP `cmd_3`;
ALTER TABLE `stream_error` DROP `event`;
--
EOL
);
    }
}
