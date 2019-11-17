<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720176 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
UPDATE `itv` set `wowza_dvr` = (`tv_archive_type` = 'wowza_dvr');
UPDATE `itv` set `flussonic_dvr` = (`tv_archive_type` = 'flussonic_dvr');
UPDATE `itv` set `nimble_dvr` = (`tv_archive_type` = 'nimble_dvr');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
