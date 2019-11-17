<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720157 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('storages')->hasColumn('dvr_type')) {
            $this->addSql("ALTER TABLE `storages` ADD COLUMN `dvr_type` ENUM('', 'stalker_dvr', 'wowza_dvr', 'flussonic_dvr', 'nimble_dvr') DEFAULT '';");
        }
        $this->addSql(<<<EOL
--

UPDATE storages set `dvr_type` = 'wowza_dvr' where `for_records` = 1 and `wowza_dvr` = 1;
UPDATE storages set `dvr_type` = 'flussonic_dvr' where `for_records` = 1 and `flussonic_dvr` = 1;
UPDATE storages set `dvr_type` = 'nimble_dvr' where `for_records` = 1 and `nimble_dvr` = 1;
UPDATE storages set `dvr_type` = 'stalker_dvr' where `for_records` = 0 and `wowza_dvr` = 0 and `flussonic_dvr` = 0 and `nimble_dvr` = 0;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `storages` DROP COLUMN `dvr_type`;
--
EOL
);
    }
}
