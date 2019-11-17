<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720194 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
INSERT INTO `adm_grp_action_access`
(    `controller_name`, `action_name`,          `is_ajax`, `description`)
VALUES  ('tv-channels', 'check-channel-name',   1,         'Checking channel name'),
        ('tv-channels', 'check-channel-number', 1,         'Checking channel number');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DELETE FROM `adm_grp_action_access` WHERE controller_name='tv-channels' AND action_name='check-channel-number';
DELETE FROM `adm_grp_action_access` WHERE controller_name='tv-channels' AND action_name='check-channel-name';
--
EOL
);
    }
}
