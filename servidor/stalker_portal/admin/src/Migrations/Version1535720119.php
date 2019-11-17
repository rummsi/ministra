<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720119 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('storages')->hasColumn('nimble_dvr')) {
            $this->addSql('ALTER TABLE `storages` ADD COLUMN `nimble_dvr` TINYINT(4) NULL DEFAULT 0;');
        }
        if (!$schema->getTable('ch_links')->hasColumn('nimble_auth_support')) {
            $this->addSql('ALTER TABLE `ch_links` ADD COLUMN `nimble_auth_support` TINYINT DEFAULT 0;');
        }
        $this->addSql(<<<EOL
--

ALTER TABLE `video_series_files` CHANGE COLUMN `tmp_link_type` `tmp_link_type` ENUM('flussonic','nginx','wowza', 'edgecast_auth', 'nimble') NULL DEFAULT NULL ;

ALTER TABLE `itv` ADD COLUMN `nimble_dvr` TINYINT(4) NULL DEFAULT 0;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `ch_links` DROP COLUMN `nimble_auth_support`;
ALTER TABLE `video_series_files` CHANGE COLUMN `tmp_link_type` `tmp_link_type` ENUM('flussonic','nginx','wowza', 'edgecast_auth') NULL DEFAULT NULL ;
ALTER TABLE `storages` DROP COLUMN `nimble_dvr`;
ALTER TABLE `itv` DROP COLUMN `nimble_dvr`;
--
EOL
);
    }
}
