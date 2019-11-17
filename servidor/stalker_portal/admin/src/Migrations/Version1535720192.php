<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720192 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
INSERT INTO `adm_grp_action_access`
(`controller_name`, `action_name`,           `is_ajax`,  `description`)
VALUES
('users',           'toggle-status',                 1, 'Status changed for users'),
('users',           'remove-users',                  1, 'Remove users'),
('users',           'change-users-reseller',         1, 'Reseller is assigned to users'),
('users',           'clear-users-reseller',          1, 'Reseller is unassigned to users');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DELETE FROM `adm_grp_action_access` WHERE controller_name='users' AND action_name='toggle-status';
DELETE FROM `adm_grp_action_access` WHERE controller_name='users' AND action_name='remove-users';
DELETE FROM `adm_grp_action_access` WHERE controller_name='users' AND action_name='change-users-reseller';
DELETE FROM `adm_grp_action_access` WHERE controller_name='users' AND action_name='clear-users-reseller';
--
EOL
);
    }
}
