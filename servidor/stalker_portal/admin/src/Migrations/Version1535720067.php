<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720067 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
INSERT INTO `adm_grp_action_access`
          (`controller_name`, `action_name`, `is_ajax`, `description`)
VALUES    ('tv-channels',      'm3u-import',         0, 'Import tv-channels from m3u-file'),
          ('tv-channels',    'get-m3u-data',         1, 'Parse m3u-file'),
          ('tv-channels',   'save-m3u-item',         1, 'Save one m3u-item as channel');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
