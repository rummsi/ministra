<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720152 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
ALTER TABLE `itv` CHANGE COLUMN `tv_archive_type` `tv_archive_type` ENUM('', 'stalker_dvr', 'wowza_dvr', 'flussonic_dvr', 'nimble_dvr') DEFAULT '';
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `itv` CHANGE COLUMN `tv_archive_type` `tv_archive_type` ENUM('', 'wowza_dvr', 'flussonic_dvr', 'nimble_dvr') DEFAULT '';
--
EOL
);
    }
}
