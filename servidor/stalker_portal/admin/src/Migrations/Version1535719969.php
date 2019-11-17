<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719969 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('video_log')->hasColumn('video_name')) {
            $this->addSql("ALTER TABLE `video_log` ADD `video_name` varchar(128) not null default '';");
        }
        if (!$schema->getTable('itv')->hasColumn('allow_pvr')) {
            $this->addSql('ALTER TABLE `itv` ADD `allow_pvr` tinyint default 0;');
        }
        $this->addSql(<<<EOL
--
ALTER TABLE `video` MODIFY `series` text;
ALTER TABLE `video` MODIFY `rate` text;
ALTER TABLE `itv` MODIFY `descr` text;
ALTER TABLE `events` MODIFY `msg` text;
ALTER TABLE `fav_itv` MODIFY `fav_ch` text;
ALTER TABLE `fav_vclub` MODIFY `fav_video` text;
ALTER TABLE `moderators_history` MODIFY `comment` text;
ALTER TABLE `itv_subscription` MODIFY `sub_ch` text;
ALTER TABLE `itv_subscription` MODIFY `bonus_ch` text;
ALTER TABLE `rss_cache_weather` MODIFY `content` text;
ALTER TABLE `rss_cache_horoscope` MODIFY `content` text;
ALTER TABLE `anec` MODIFY `anec_body` text;
ALTER TABLE `course_cache` MODIFY `content` text;
ALTER TABLE `storage_cache` MODIFY `storage_data` text;
ALTER TABLE `user_modules` MODIFY `restricted` text;
ALTER TABLE `user_modules` MODIFY `disabled` text;
ALTER TABLE `censored_channels` MODIFY `list` text;
ALTER TABLE `censored_channels` MODIFY `exclude` text;
ALTER TABLE `storages_failure` MODIFY `description` text;
ALTER TABLE `developer_api_key` MODIFY `comment` text;
ALTER TABLE `user_downloads` MODIFY `downloads` text;
ALTER TABLE `media_favorites` MODIFY `favorites` text;
ALTER TABLE `services_package` MODIFY `description` text;
ALTER TABLE `users` MODIFY `comment` text;

UPDATE `itv` SET allow_pvr=1 WHERE mc_cmd!='';

EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `itv` DROP `allow_pvr`;
ALTER TABLE `video_log` DROP `video_name`;
--
EOL
);
    }
}
