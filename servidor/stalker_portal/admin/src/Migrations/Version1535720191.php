<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720191 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
DELETE FROM `adm_grp_action_access` WHERE controller_name='index' AND action_name='index-datatable1-list-json';
DELETE FROM `adm_grp_action_access` WHERE controller_name='index' AND action_name='index-datatable2-list-json';
DELETE FROM `adm_grp_action_access` WHERE controller_name='index' AND action_name='index-datatable3-list-json';
INSERT INTO `adm_grp_action_access`
(`controller_name`, `action_name`,       `is_ajax`, `view_access`,  `edit_access`, `action_access`,                 `description`,         `hidden`)
VALUES  ('index',   'datatable-devices',         1,              1,              1,              1, 'Getting data for devices table',             1),
        ('index',   'datatable-content',         1,              1,              1,              1, 'Getting data for content table',             1),
        ('index',   'datatable-licenses',        1,              1,              1,              1, 'Getting data for licenses keys table',       1),
        ('index',   'datatable-storages',        1,              1,              1,              1, 'Getting data for Storages',                  1),
        ('index',   'datatable-streaming',       1,              1,              1,              1, 'Getting data for Streaming',                 1);
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DELETE FROM `adm_grp_action_access` WHERE controller_name='index' AND action_name='datatable-devices';
DELETE FROM `adm_grp_action_access` WHERE controller_name='index' AND action_name='datatable-content';
DELETE FROM `adm_grp_action_access` WHERE controller_name='index' AND action_name='datatable-licenses';
DELETE FROM `adm_grp_action_access` WHERE controller_name='index' AND action_name='datatable-storages';
DELETE FROM `adm_grp_action_access` WHERE controller_name='index' AND action_name='datatable-streaming';
INSERT INTO `adm_grp_action_access`
(`controller_name`, `action_name`,                 `is_ajax`,  `view_access`,  `edit_access`, `action_access`,                 `description`, `hidden`)
VALUES  ('index',    'index-datatable1-list-json',         1,              1,              1,               1, 'Getting data for index page',        1),
        ('index',    'index-datatable2-list-json',         1,              1,              1,               1, 'Getting data for index page',        1),
        ('index',    'index-datatable3-list-json',         1,              1,              1,               1, 'Getting data for index page',        1);
--
EOL
);
    }
}
