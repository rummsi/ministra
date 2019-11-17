<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720153 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
UPDATE itv set `tv_archive_type` = 'stalker_dvr' where `enable_tv_archive` = 1 and `wowza_dvr` = 0 and `flussonic_dvr` = 0 and `nimble_dvr` = 0;
INSERT INTO `adm_grp_action_access`
        (`controller_name`,                         `action_name`,    `is_ajax`, `description`                  )
VALUES  ('index',               'check-certificate-server-health',            1, 'Checking the health status of the certificate server');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
