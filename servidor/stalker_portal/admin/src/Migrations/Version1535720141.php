<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720141 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
INSERT INTO `adm_grp_action_access`
        (`controller_name`,     `action_name`,                `is_ajax`, `description`)
VALUES  ('new-video-club',      'add-video-season',                   1, 'Adding seasons into video'),
        ('new-video-club',      'add-video-season-series',            1, 'Adding episodes into season'),
        ('new-video-club',      'get-one-video-file-json',            1, 'Getting info by one file of episode'),
        ('new-video-club',      'get-video-files-list-json',          1, 'Getting list of files of episode'),
        ('new-video-club',      'get-video-season-list-json',         1, 'Getting list of seasons and episodes of video'),
        ('new-video-club',      'remove-video-data',                  1, 'Deleting info about one video file from database'),
        ('new-video-club',      'save-season-series-names',           1, 'Change names and original names for episodes and seasons'),
        ('new-video-club',      'save-video-files',                   1, 'Adding and editing files of episode'),
        ('new-video-club',      'toggle-video-accessed',              1, 'Change accessed status of one file of episode');
CREATE TABLE `agaa_tmp`
  AS
    SELECT *
    FROM `adm_grp_action_access`
    WHERE `controller_name` = 'new-video-club' AND `action_name` IN ('add-video-season', 'add-video-season-series', 'get-one-video-file-json', 'get-video-files-list-json', 'get-video-season-list-json', 'remove-video-data', 'save-season-series-names', 'save-video-files', 'toggle-video-accessed')
    GROUP BY concat(`controller_name`, `action_name`)
    ORDER BY concat(`controller_name`, `action_name`) ASC;
ALTER TABLE `agaa_tmp` DROP COLUMN `id`;
UPDATE `agaa_tmp` SET `is_ajax` = 1, `view_access` = 0, `edit_access` = 0, `action_access` = 0, `group_id` = 0;
ALTER TABLE `agaa_tmp` ADD COLUMN `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
SELECT @max := MAX(`id`) FROM `adm_grp_action_access`;
UPDATE `agaa_tmp` SET `id` = `id` + @max;
INSERT INTO `adm_grp_action_access` SELECT * FROM `agaa_tmp`;
DROP TABLE `agaa_tmp`;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
}
