<?php

require __DIR__ . '/common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Video;
$from_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('max(id) as max_id')->from('vclub_not_ended')->where(['added<' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() - 1209600)])->get()->first('max_id');
if ($from_id) {
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('vclub_not_ended', ['id<' => $from_id]);
}
if (\Ministra\Lib\Video::isNotEndedHistoryEnabled()) {
    $from_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('max(id) as max_id')->from('user_played_movies')->where(['watched' => 0, 'playtime<' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() - 1209600)])->get()->first('max_id');
    if ($from_id) {
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('user_played_movies', ['id<' => $from_id, 'watched' => 0]);
    }
}
if (\Ministra\Lib\Video::isWatchedHistoryEnabled() && \Ministra\Lib\Video::getWatchedHistorySize()) {
    $history_size = \Ministra\Lib\Video::getWatchedHistorySize() * 24 * 60 * 60;
    $from_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('max(id) as max_id')->from('user_played_movies')->where(['watched' => 1, 'playtime<' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() - $history_size)])->get()->first('max_id');
    if ($from_id) {
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('user_played_movies', ['id<' => $from_id, 'watched' => 1]);
    }
}
$from_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('max(id) as max_id')->from('tv_reminder')->where(['fire_time<' => 'now()'])->get()->first('max_id');
if ($from_id) {
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('tv_reminder', ['id<' => $from_id]);
}
$from_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('max(id) as max_id')->from('storages_failure')->where(['added<' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() - 604800)])->get()->first('max_id');
if ($from_id) {
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('storages_failure', ['id<' => $from_id]);
}
$from_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('max(id) as max_id')->from('played_video')->where(['playtime<' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() - 5184000)])->get()->first('max_id');
if ($from_id) {
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('played_video', ['id<' => $from_id]);
}
$from_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('max(id) as max_id')->from('played_itv')->where(['playtime<' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() - 5184000)])->get()->first('max_id');
if ($from_id) {
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('played_itv', ['id<' => $from_id]);
}
$from_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('max(id) as max_id')->from('played_tv_archive')->where(['playtime<' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() - 5184000)])->get()->first('max_id');
if ($from_id) {
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('played_tv_archive', ['id<' => $from_id]);
}
$from_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('max(id) as max_id')->from('media_claims_log')->where(['added<' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() - 5184000)])->get()->first('max_id');
if ($from_id) {
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('media_claims_log', ['id<' => $from_id]);
}
if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('use_optimize_table', \true)) {
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `events` DROP INDEX `eventtime`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `events` DROP INDEX `uid`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `events` DROP INDEX `ended`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('OPTIMIZE TABLE `events`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `events` ADD INDEX `eventtime` (`eventtime`)');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `events` ADD INDEX `uid` (`uid`)');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `events` ADD INDEX `ended` (`ended`)');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `vclub_not_ended` DROP INDEX `uid`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('OPTIMIZE TABLE `vclub_not_ended`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `vclub_not_ended` ADD INDEX `uid` (`uid`, `video_id`)');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `tv_reminder` DROP INDEX `tv_program_id`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `tv_reminder` DROP INDEX `tv_program_real_id`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `tv_reminder` DROP INDEX `ch_id_real_id`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `tv_reminder` DROP INDEX `mac_time`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('OPTIMIZE TABLE `tv_reminder`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `tv_reminder` ADD INDEX `tv_program_id` (`tv_program_id`)');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `tv_reminder` ADD INDEX `tv_program_real_id` (`tv_program_real_id`)');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `tv_reminder` ADD INDEX `ch_id_real_id` (`tv_program_real_id`, `ch_id`)');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `tv_reminder` ADD INDEX `mac_time` (`mac`, `fire_time`)');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `storages_failure` DROP INDEX `storage`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('OPTIMIZE TABLE `storages_failure`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `storages_failure` ADD INDEX `storage` (`storage_id`, `added`)');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `played_video` DROP INDEX `video_id_playtime`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('OPTIMIZE TABLE `played_video`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `played_video` ADD INDEX `video_id_playtime` (`video_id`, `playtime`)');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('OPTIMIZE TABLE `played_itv`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('OPTIMIZE TABLE `played_tv_archive`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `media_claims_log` DROP INDEX `added`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('OPTIMIZE TABLE `media_claims_log`');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('ALTER TABLE `media_claims_log` ADD INDEX `added` (`added`)');
}
