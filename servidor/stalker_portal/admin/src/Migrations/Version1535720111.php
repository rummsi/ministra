<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720111 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
INSERT INTO `adm_grp_action_access`
          (`controller_name`,                          `action_name`, `is_ajax`, `description`)
VALUES    ('new-video-club',         'check-video-categories-number',         1, 'Validation of number of the video-category'),
          ('new-video-club',         'delete-video-season',                   1, 'Deleting season of video serial with series and files'),
          ('new-video-club',         'delete-video-season-series',            1, 'Deleting series of video serial with files'),
          ('users',                           'get-subscribed-tv',            1, 'Getting users available and subscribed tv-channels');
UPDATE `apps_tos` SET `tos_en`=replace(`tos_en`, '2626 East 14</span><sup><span>th</span></sup><span> Street, Brooklyn, New York 11235', '174 Bay 49</span><sup><span>th</span></sup><span> Street, Brooklyn, NY 11214');
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
