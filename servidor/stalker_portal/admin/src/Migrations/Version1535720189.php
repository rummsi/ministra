<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720189 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
INSERT INTO `adm_grp_action_access`
        (`controller_name`,                `action_name`, `is_ajax`,                                                               `description`, `only_top_admin`)
VALUES  (         'admins',       'resellers-check-name',         1,                                 'Check if available this name for reseller',                1),
        (         'admins','check-ip-range-intersection',         1, 'Сheck for use of the current address or address range by another reseller',                1);
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
