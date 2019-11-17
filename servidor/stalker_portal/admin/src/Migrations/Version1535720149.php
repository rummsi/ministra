<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720149 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('itv')->hasColumn('tv_archive_type')) {
            $this->addSql("ALTER TABLE `itv` ADD COLUMN `tv_archive_type` ENUM('', 'wowza_dvr', 'flussonic_dvr', 'nimble_dvr') DEFAULT '';");
        }
        $this->addSql(<<<EOL
--

UPDATE itv set `tv_archive_type` = 'wowza_dvr' where `enable_tv_archive` = 1 and `wowza_dvr` = 1;
UPDATE itv set `tv_archive_type` = 'flussonic_dvr' where `enable_tv_archive` = 1 and `flussonic_dvr` = 1;
UPDATE itv set `tv_archive_type` = 'nimble_dvr' where `enable_tv_archive` = 1 and `nimble_dvr` = 1;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `itv` DROP COLUMN `tv_archive_type`;
--
EOL
);
    }
}
