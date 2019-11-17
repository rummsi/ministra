<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719967 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('radio')->hasColumn('volume_correction')) {
            $this->addSql('ALTER TABLE `radio` ADD `volume_correction` int NOT NULL default 0;');
        }
        $this->addSql(<<<EOL
--
ALTER TABLE `itv` ADD INDEX base_ch (`base_ch`);
ALTER TABLE `ch_links` ADD INDEX (`ch_id`);
ALTER TABLE `ch_links` ADD INDEX ch_id_status (`ch_id`, `status`);
ALTER TABLE `epg` ADD INDEX real_id (`real_id`);
ALTER TABLE `tv_reminder` ADD INDEX tv_program_real_id (`tv_program_real_id`);
ALTER TABLE `tv_reminder` ADD INDEX ch_id_real_id (`tv_program_real_id`, `ch_id`);
ALTER TABLE `tv_reminder` ADD INDEX mac_time (`mac`, `fire_time`);
ALTER TABLE `storages` ADD INDEX status_simple (`status`, `for_simple_storage`);

EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `itv` DROP INDEX base_ch;
ALTER TABLE `ch_links` DROP INDEX ch_id;
ALTER TABLE `ch_links` DROP INDEX ch_id_status;
ALTER TABLE `epg` DROP INDEX real_id;
ALTER TABLE `tv_reminder` DROP INDEX tv_program_real_id;
ALTER TABLE `tv_reminder` DROP INDEX ch_id_real_id;
ALTER TABLE `tv_reminder` DROP INDEX mac_time;
ALTER TABLE `storages` DROP INDEX status_simple;
ALTER TABLE `radio` DROP `volume_correction`;
--
EOL
);
    }
}
