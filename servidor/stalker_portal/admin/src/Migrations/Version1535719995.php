<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719995 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('itv')->hasColumn('allow_local_timeshift')) {
            $this->addSql('ALTER TABLE `itv` ADD `allow_local_timeshift` tinyint NOT NULL default 0;');
        }
        if (!$schema->getTable('users')->hasColumn('ts_delay')) {
            $this->addSql("ALTER TABLE `users` ADD `ts_delay` varchar(64) NOT NULL default 'on_pause';");
        }
        if (!$schema->getTable('users')->hasColumn('ts_action_on_exit')) {
            $this->addSql("ALTER TABLE `users` ADD `ts_action_on_exit` varchar(64) NOT NULL default 'no_save';");
        }
        if (!$schema->getTable('users')->hasColumn('ts_buffer_use')) {
            $this->addSql("ALTER TABLE `users` ADD `ts_buffer_use` varchar(128) NOT NULL default 'cyclic';");
        }
        if (!$schema->getTable('users')->hasColumn('ts_max_length')) {
            $this->addSql('ALTER TABLE `users` ADD `ts_max_length` int NOT NULL default 3600;');
        }
        if (!$schema->getTable('users')->hasColumn('ts_path')) {
            $this->addSql("ALTER TABLE `users` ADD `ts_path` varchar(266) NOT NULL default '';");
        }
        if (!$schema->getTable('users')->hasColumn('ts_enable_icon')) {
            $this->addSql('ALTER TABLE `users` ADD `ts_enable_icon` tinyint NOT NULL default 1;');
        }
        if (!$schema->getTable('users')->hasColumn('ts_enabled')) {
            $this->addSql('ALTER TABLE `users` ADD `ts_enabled` tinyint NOT NULL default 0;');
        }
        $this->addSql(<<<EOL
--
UPDATE `itv` SET `allow_local_timeshift`=`allow_local_pvr`;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users` DROP `ts_enabled`;
ALTER TABLE `users` DROP `ts_enable_icon`;
ALTER TABLE `users` DROP `ts_path`;
ALTER TABLE `users` DROP `ts_max_length`;
ALTER TABLE `users` DROP `ts_buffer_use`;
ALTER TABLE `users` DROP `ts_action_on_exit`;
ALTER TABLE `users` DROP `ts_delay`;
ALTER TABLE `itv` DROP `allow_local_timeshift`;
--
EOL
);
    }
}
