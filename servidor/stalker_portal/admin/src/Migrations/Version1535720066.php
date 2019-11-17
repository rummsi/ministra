<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720066 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
INSERT INTO `adm_grp_action_access`
          (`controller_name`, `action_name`,                        `is_ajax`, `description`)
VALUES    ('tv-channels',     'iptv-list-json',                             1, 'List of tv-channels by page + filters'),
          ('video-club',      'video-schedule-list-json',                   1, 'Schedule switch on of movie by page + filters'),
          ('video-club',      'video-advertise-list-json',                  1, 'List of advertising blocks by page + filters'),
          ('video-club',      'video-moderators-addresses-list-json',       1, 'List of STBs of video-moderators by page + filters'),
          ('users',           'users-consoles-groups-list-json',            1, 'List of STB by page + filters');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
