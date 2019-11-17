<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720138 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
INSERT INTO `adm_grp_action_access`
        (`controller_name`,               `action_name`,    `is_ajax`, `description`                  )
VALUES  ('new-video-club',    'get-one-video-file-json',            1, 'Getting info by one file of episode'),
        ('new-video-club',  'get-video-files-list-json',            1, 'Getting list of files of episode'),
        ('new-video-club', 'get-video-season-list-json',            1, 'Getting list of seasons and episodes of video'),
        ('new-video-club',        'get-media-info-json',            1, 'Getting media-info from source'),
        ('audio-club',            'get-media-info-json',            1, 'Getting media-info from source');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
