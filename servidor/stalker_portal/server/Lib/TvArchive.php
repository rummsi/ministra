<?php

namespace Ministra\Lib;

use DateInterval;
use DateTime;
use DateTimeZone;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
use Ministra\Lib\DVR\FlussonicDVR;
use Ministra\Lib\DVR\NimbleDVR;
class TvArchive extends \Ministra\Lib\Master implements \Ministra\Lib\StbApi\TvArchive
{
    public function __construct()
    {
        $this->media_type = 'tv_archive';
        $this->db_table = 'tv_archive';
        parent::__construct();
    }
    public static function checkTemporaryToken($token)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance()->get($token);
    }
    public static function checkTemporaryTimeShiftToken($key)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance()->get($key);
    }
    public static function getArchiveRange($chID = 0)
    {
        return (int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('tv_archive_parts_number');
    }
    public static function getTaskByChannelId($chID)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tv_archive')->where(['ch_id' => $chID])->get()->first();
    }
    public static function getTasksByChannelId($chID)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tv_archive')->where(['ch_id' => $chID])->get()->all();
    }
    public function createLink()
    {
        $res = ['id' => 0, 'cmd' => '', 'storage_id' => '', 'load' => '0', 'error' => ''];
        \preg_match("/\\/media\\/(\\d+).mpg/", $_REQUEST['cmd'], $tmp_arr);
        $programID = $tmp_arr[1];
        $program = \Ministra\Lib\Epg::getById($programID);
        try {
            $task = $this->getLessLoadedTaskByChId($program['ch_id']);
        } catch (\Ministra\Lib\StorageSessionLimitException $e) {
            $res['error'] = 'limit';
            $res['storage_name'] = $e->getStorageName();
            return $res;
        }
        $overlap = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('tv_archive_playback_overlap', 0) * 60;
        $overlap_start = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('tv_archive_playback_overlap_start', 0) * 60;
        $tz = new \DateTimeZone(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::$server_timezone);
        $date = new \DateTime(\date('r', \strtotime($program['time'])));
        $date->setTimezone($tz);
        if ($overlap_start) {
            $date->sub(new \DateInterval('PT' . $overlap_start . 'S'));
        }
        $date_now = new \DateTime('now', new \DateTimeZone(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::$server_timezone));
        $date_to = new \DateTime(\date('r', \strtotime($program['time_to'])));
        $date_to->setTimezone($tz);
        $dst_diff = $date->format('Z') - $date_now->format('Z');
        $storage = \Ministra\Lib\Master::getStorageByName($task['storage_name']);
        if (!\array_key_exists('dvr_type', $storage)) {
            $storage['dvr_type'] = '';
        }
        if (empty($storage['dvr_type']) || $storage['dvr_type'] == 'stalker_dvr') {
            if ($dst_diff > 0) {
                $date->add(new \DateInterval('PT' . $dst_diff . 'S'));
                $date_to->add(new \DateInterval('PT' . $dst_diff . 'S'));
            } elseif ($dst_diff < 0) {
                $dst_diff *= -1;
                $date->sub(new \DateInterval('PT' . $dst_diff . 'S'));
                $date_to->sub(new \DateInterval('PT' . $dst_diff . 'S'));
            }
        }
        $start_timestamp = $date->getTimestamp();
        $stop_timestamp = $date_to->getTimestamp() + $overlap;
        $channel = \Ministra\Lib\Itv::getChannelById($program['ch_id']);
        $filename = $date->format('Ymd-H');
        if ($channel['tv_archive_type'] == 'wowza_dvr') {
            $filename .= '.mp4';
        } else {
            $filename .= '.mpg';
        }
        $res['storage_id'] = $storage['id'];
        $position = \date('i', $start_timestamp) * 60 + \date('s', $start_timestamp);
        if ($storage['dvr_type'] == 'flussonic_dvr') {
            $dvr = new \Ministra\Lib\DVR\FlussonicDVR($channel['mc_cmd'], $storage['storage_ip']);
            $duration = $stop_timestamp - $start_timestamp;
            if ($dvr->isValid()) {
                $link = $dvr->getArchiveLink($start_timestamp, $duration);
                $res['cmd'] = $link . '?token=' . $this->createTemporaryToken($this->stb->id);
                $res['download_cmd'] = $dvr->getDownloadLinkTs($start_timestamp, $duration);
            } else {
                echo $dvr->ErrorMessage();
                $res['error'] = 'server_error';
            }
        } elseif ($storage['dvr_type'] == 'wowza_dvr') {
            if (\preg_match("/:\\/\\/([^\\/]*)\\/.*\\.m3u8/", $channel['mc_cmd'], $match)) {
                $res['cmd'] = \preg_replace('/:\\/\\/([^\\/]*)/', '://' . $storage['storage_ip'], $channel['mc_cmd']);
                $replacement = '.m3u8?DVR&wowzadvrplayliststart=' . \gmdate('YmdHis', $start_timestamp) . '&wowzadvrplaylistduration=' . ($stop_timestamp - $start_timestamp) * 1000;
                $res['cmd'] = \preg_replace('/\\.m3u8.*/', $replacement, $res['cmd']) . '&token=' . $this->createTemporaryToken('1');
                $res['cmd'] .= '&' . \Ministra\Lib\Itv::getWowzaSecureToken($res['cmd'], \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('wowza_securetoken_vod_endtime', 0));
                $res['download_cmd'] = false;
            } else {
                $res['error'] = 'link_fault';
            }
        } elseif ($storage['dvr_type'] == 'nimble_dvr') {
            if (\preg_match("/:\\/\\/([^\\/]*)\\/.*\\.(mpd|m3u8)/", $channel['mc_cmd'], $match)) {
                $res['cmd'] = \preg_replace('/:\\/\\/([^\\/]*)/', '://' . $storage['storage_ip'], $channel['mc_cmd']);
                $res['cmd'] = \preg_replace('/\\.' . $match[2] . '.*/', '_range-' . $start_timestamp . '-' . ($stop_timestamp - $start_timestamp) . '.' . $match[2], $res['cmd']) . '?token=' . $this->createTemporaryToken('1');
                $res['cmd'] .= '&' . \preg_match('/https?\\:\\/\\//i', $res['cmd']) ? \Ministra\Lib\Itv::getNimbleHttpAuthToken($res['cmd']) : \Ministra\Lib\Itv::getNimbleRtspAuthToken($res['cmd']);
                $res['download_cmd'] = false;
            } else {
                $res['error'] = 'link_fault';
            }
        } else {
            $res['cmd'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('tv_archive_player_solution', 'ffmpeg') . ' http://' . $storage['storage_ip'] . ':' . $storage['apache_port'] . '/stalker_portal/storage/get.php?filename=' . $filename . '&token=' . $this->createTemporaryToken(true);
            if (!empty($_REQUEST['download'])) {
                $downloads = new \Ministra\Lib\Downloads();
                $res['download_cmd'] = $downloads->createDownloadLink('tv_archive', $programID, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
            } else {
                $res['download_cmd'] = false;
            }
        }
        $res['cmd'] .= '&ch_id=' . $program['ch_id'] . '&start=' . $position . '&duration=' . ($stop_timestamp - $start_timestamp) . '&osd_title=' . \urlencode($channel['name'] . ' — ' . $program['name']) . '&real_id=' . $program['real_id'];
        $res['to_file'] = \date('Ymd-H', $start_timestamp) . '_' . \Ministra\Lib\System::transliterate($channel['name'] . '_' . $program['name']) . '.mpg';
        return $res;
    }
    private function getLessLoadedTaskByChId($chID, $ignore_session_limit = false)
    {
        $tasks = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tv_archive')->where(['ch_id' => $chID])->get()->all();
        $tasks_map = [];
        foreach ($tasks as $task) {
            $tasks_map[$task['storage_name']] = $task;
        }
        $all_storages = \array_keys($this->storages);
        $task_storages = \array_keys($tasks_map);
        $intersection = \array_intersect($all_storages, $task_storages);
        $intersection = \array_values($intersection);
        if (empty($intersection)) {
            return false;
        }
        if ($this->storages[$intersection[0]]['load'] >= 1 && !$ignore_session_limit) {
            $this->incrementStorageDeny($intersection[0]);
            throw new \Ministra\Lib\StorageSessionLimitException($intersection[0]);
        }
        return $tasks_map[$intersection[0]];
    }
    private function createTemporaryToken($val)
    {
        $key = \md5($val . \microtime(1) . \uniqid());
        $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
        $result = $cache->set($key, $val, 0, 28800);
        if ($result) {
            return $key;
        }
        return $result;
    }
    public function getNextPartUrl()
    {
        $programID = $_REQUEST['id'];
        if (!$programID) {
            return false;
        }
        $program = \Ministra\Lib\Epg::getByRealId($programID);
        if (empty($program)) {
            if (\preg_match("/(\\d+)_(\\d+)/", $programID, $match)) {
                $next = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('epg')->where(['ch_id' => (int) $match[1], 'time>' => \date('Y-m-d H:i:s', (int) $match[2])])->orderby('time')->limit(1)->get()->first();
            } else {
                return false;
            }
        } else {
            $next = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('epg')->where(['ch_id' => $program['ch_id'], 'time>' => $program['time']])->orderby('time')->limit(1)->get()->first();
        }
        if (empty($next)) {
            return false;
        }
        try {
            if ($next['time'] != $program['time_to'] && !isset($match)) {
                $program = ['name' => '[' . \_('Break in the program') . ']', 'ch_id' => $next['ch_id'], 'time' => $program['time_to'], 'time_to' => $next['time'], 'real_id' => $next['ch_id'] . '_' . \strtotime($program['time_to'])];
                return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('tv_archive_player_solution', 'ffmpeg') . ' ' . $this->getUrlByProgramId(0, true, $program);
            }
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('tv_archive_player_solution', 'ffmpeg') . ' ' . $this->getUrlByProgramId($next['id'], true);
        } catch (\Ministra\Lib\StorageSessionLimitException $e) {
            return false;
        }
    }
    public function getUrlByProgramId($programID, $disableOverlap = false, $program = array())
    {
        if (empty($program)) {
            $program = \Ministra\Lib\Epg::getById($programID);
        }
        $task = $this->getLessLoadedTaskByChId($program['ch_id']);
        $overlap = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('tv_archive_playback_overlap', 0) * 60;
        $overlap_start = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('tv_archive_playback_overlap_start', 0) * 60;
        $tz = new \DateTimeZone(!empty(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::$server_timezone) ? \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::$server_timezone : \date_default_timezone_get());
        $date = new \DateTime(\date('r', \strtotime($program['time'])));
        $date->setTimezone($tz);
        if ($disableOverlap) {
            if ($overlap) {
                $date->add(new \DateInterval('PT' . $overlap . 'S'));
            }
        } else {
            if ($overlap_start) {
                $date->sub(new \DateInterval('PT' . $overlap_start . 'S'));
            }
        }
        $date_now = new \DateTime('now', new \DateTimeZone(!empty(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::$server_timezone) ? \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::$server_timezone : \date_default_timezone_get()));
        $date_to = new \DateTime(\date('r', \strtotime($program['time_to'])));
        $date_to->setTimezone($tz);
        $dst_diff = $date->format('Z') - $date_now->format('Z');
        $storage = \Ministra\Lib\Master::getStorageByName($task['storage_name']);
        if (!\array_key_exists('dvr_type', $storage)) {
            $storage['dvr_type'] = '';
        }
        if (empty($storage['dvr_type']) || $storage['dvr_type'] == 'stalker_dvr') {
            if ($dst_diff > 0) {
                $date->add(new \DateInterval('PT' . $dst_diff . 'S'));
                $date_to->add(new \DateInterval('PT' . $dst_diff . 'S'));
            } elseif ($dst_diff < 0) {
                $dst_diff *= -1;
                $date->sub(new \DateInterval('PT' . $dst_diff . 'S'));
                $date_to->sub(new \DateInterval('PT' . $dst_diff . 'S'));
            }
        }
        $start_timestamp = $date->getTimestamp();
        $stop_timestamp = $date_to->getTimestamp() + $overlap;
        $channel = \Ministra\Lib\Itv::getChannelById($program['ch_id']);
        $filename = $date->format('Ymd-H');
        if ($channel['tv_archive_type'] == 'wowza_dvr') {
            $filename .= '.mp4';
        } else {
            $filename .= '.mpg';
        }
        $position = \date('i', $start_timestamp) * 60 + \date('s', $start_timestamp);
        $channel = \Ministra\Lib\Itv::getChannelById($program['ch_id']);
        $url = false;
        if ($storage['dvr_type'] == 'flussonic_dvr') {
            $dvr = new \Ministra\Lib\DVR\FlussonicDVR($channel['mc_cmd'], $storage['storage_ip']);
            if ($dvr->isValid()) {
                $duration = $stop_timestamp - $start_timestamp;
                $url = $dvr->getArchiveLink($start_timestamp, $duration);
                $url .= '?token=' . $this->createTemporaryToken($this->stb->id);
            }
        } elseif ($storage['dvr_type'] == 'wowza_dvr') {
            if (\preg_match("/:\\/\\/([^\\/]*)\\/.*\\.m3u8/", $channel['mc_cmd'], $match)) {
                $url = \preg_replace('/:\\/\\/([^\\/]*)/', '://' . $storage['storage_ip'], $channel['mc_cmd']);
                $url = \preg_replace('/\\.m3u8.*/', '.m3u8?DVR&wowzadvrplayliststart=' . \gmdate('YmdHis', $start_timestamp) . '&wowzadvrplaylistduration=' . ($stop_timestamp - $start_timestamp) * 1000, $url) . '&token=' . $this->createTemporaryToken('1');
                $url .= '&' . \Ministra\Lib\Itv::getWowzaSecureToken($url, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('wowza_securetoken_vod_endtime', 0));
            }
        } elseif ($storage['dvr_type'] == 'nimble_dvr') {
            if (\preg_match("/:\\/\\/([^\\/]*)\\/.*\\.(mpd|m3u8)/", $channel['mc_cmd'], $match)) {
                $url = \preg_replace('/:\\/\\/([^\\/]*)/', '://' . $storage['storage_ip'], $channel['mc_cmd']);
                $replacement = '_range-' . $start_timestamp . '-' . ($stop_timestamp - $start_timestamp) . '.' . $match[2];
                $url = \preg_replace('/\\.' . $match[2] . '.*/', $replacement, $url) . '?token=' . $this->createTemporaryToken('1');
                $url .= '&' . \preg_match('/https?\\:\\/\\//i', $url) ? \Ministra\Lib\Itv::getNimbleHttpAuthToken($url) : \Ministra\Lib\Itv::getNimbleRtspAuthToken($url);
            }
        } else {
            $url = 'http://' . $storage['storage_ip'] . ':' . $storage['apache_port'] . '/stalker_portal/storage/get.php?filename=' . $filename . '&token=' . $this->createTemporaryToken(true);
        }
        if ($url !== false) {
            $url .= '&ch_id=' . $program['ch_id'] . '&start=' . $position . '&duration=' . ($stop_timestamp - $start_timestamp) . '&osd_title=' . \urlencode($channel['name'] . ' — ' . $program['name']) . '&real_id=' . $program['real_id'];
        }
        if (!empty($storage['storage_name'])) {
            $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
            $cache->set($this->stb->id . '_playback', ['type' => 'tv-archive', 'id' => $programID, 'storage' => $storage['storage_name'], 'storage_id' => $storage['id']], 0, 10);
        } else {
            $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
            $cache->del($this->stb->id . '_playback');
        }
        return $url;
    }
    public function getLinkForChannel()
    {
        $chID = (int) $_REQUEST['ch_id'];
        $res = ['id' => 0, 'cmd' => '', 'storage_id' => '', 'load' => '0', 'error' => ''];
        try {
            $task = $this->getLessLoadedTaskByChId($chID);
        } catch (\Ministra\Lib\StorageSessionLimitException $e) {
            $res['error'] = 'limit';
            $res['storage_name'] = $e->getStorageName();
            return $res;
        }
        if (empty($task)) {
            $res['error'] = 'server_error';
            return $res;
        }
        $storage = \Ministra\Lib\Master::getStorageByName($task['storage_name']);
        $tz = new \DateTimeZone(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::$server_timezone);
        $date = new \DateTime(\date('r'));
        $date->setTimezone($tz);
        $date_now = new \DateTime('now', new \DateTimeZone(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::$server_timezone));
        $dst_diff = $date->format('Z') - $date_now->format('Z');
        if (!\array_key_exists('dvr_type', $storage)) {
            $storage['dvr_type'] = '';
        }
        if (empty($storage['dvr_type']) || $storage['dvr_type'] !== 'flussonic_dvr' || $storage['dvr_type'] !== 'wowza_dvr') {
            if ($dst_diff > 0) {
                $date->add(new \DateInterval('PT' . $dst_diff . 'S'));
            } elseif ($dst_diff < 0) {
                $dst_diff *= -1;
                $date->sub(new \DateInterval('PT' . $dst_diff . 'S'));
            }
        }
        $position = (int) $date->format('i') * 60 + (int) $date->format('s');
        $channel = \Ministra\Lib\Itv::getChannelById($chID);
        $filename = $date->format('Ymd-H');
        $filename .= '.mpg';
        $abs = \strtotime(\date('Y-m-d H:00:00'));
        $now = \time();
        if ($channel['tv_archive_type'] == 'flussonic_dvr') {
            $dvr = new \Ministra\Lib\DVR\FlussonicDVR($channel['mc_cmd'], $storage['storage_ip']);
            if ($dvr->isValid()) {
                $link = $dvr->getType() == 'ts' ? $dvr->getArchiveLink($abs, $now - $abs) : $dvr->getRewindingHlsLink($abs);
                $res['cmd'] = $link . '?token=' . $this->createTemporaryToken($this->stb->id);
            } else {
                $res['error'] = 'server_error';
            }
        } elseif ($channel['tv_archive_type'] == 'wowza_dvr') {
            if (\preg_match("/:\\/\\/([^\\/]*)\\/.*\\.m3u8/", $channel['mc_cmd'], $match)) {
                $url = \preg_replace('/:\\/\\/([^\\/]*)/', '://' . $storage['storage_ip'], $channel['mc_cmd']);
                $res['cmd'] = \preg_replace('/\\.m3u8.*/', '.m3u8?DVR&wowzadvrplayliststart=' . \gmdate('YmdH0000') . '&wowzadvrplaylistduration=3600000', $url) . '&token=' . $this->createTemporaryToken('1');
                $res['cmd'] .= '&' . \Ministra\Lib\Itv::getWowzaSecureToken($res['cmd'], \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('wowza_securetoken_vod_endtime', 0));
            } else {
                $res['error'] = 'server_error';
            }
        } elseif ($channel['tv_archive_type'] == 'nimble_dvr') {
            $dvr = new \Ministra\Lib\DVR\NimbleDVR($channel['mc_cmd'], $storage['storage_ip']);
            if ($dvr->isValid()) {
                $link = $dvr->getArchiveLink($abs, $now - $abs);
                $authToken = \preg_match('/https?\\:\\/\\//i', $link) ? \Ministra\Lib\Itv::getNimbleHttpAuthToken($link) : \Ministra\Lib\Itv::getNimbleRtspAuthToken($link);
                $link .= '?' . $authToken;
                $res['cmd'] = $link;
            } else {
                $res['error'] = 'server_error';
            }
        } else {
            $res['cmd'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('tv_archive_player_solution', 'ffmpeg') . " http://{$storage['storage_ip']}";
            if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_timeshift_tmp_link', false)) {
                $link_result = $this->createTemporaryTimeShiftToken('/archive/' . $chID . '/' . $filename);
                $res['cmd'] .= '/tslink/' . $link_result . '/archive/';
            } else {
                $res['cmd'] .= '/archive/';
            }
            $res['cmd'] .= $chID . '/' . $filename;
        }
        $res['cmd'] .= ' position:' . $position . ' media_len:' . ((int) \date('H') * 3600 + (int) \date('i') * 60 + (int) \date('s'));
        return $res;
    }
    private function createTemporaryTimeShiftToken($url)
    {
        $key = \md5($url . \time() . \uniqid());
        $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
        $result = $cache->set($key, $url, 0, 28800);
        if ($result) {
            return $key;
        }
        return $result;
    }
    public function setPlayed()
    {
        return $this->db->insert('played_tv_archive', ['ch_id' => (int) $_REQUEST['ch_id'], 'uid' => $this->stb->id, 'playtime' => 'NOW()'])->insert_id();
    }
    public function updatePlayedEndTime()
    {
        $played_tv_archive = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('played_tv_archive')->where(['id' => (int) $_REQUEST['hist_id']])->get()->first();
        if (!empty($played_tv_archive)) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('played_tv_archive', ['length' => \time() - \strtotime($played_tv_archive['playtime'])], ['id' => (int) $_REQUEST['hist_id']]);
        }
        return false;
    }
    public function setPlayedTimeshift()
    {
        return $this->db->insert('played_timeshift', ['ch_id' => (int) $_REQUEST['ch_id'], 'uid' => $this->stb->id, 'playtime' => 'NOW()'])->insert_id();
    }
    public function updatePlayedTimeshiftEndTime()
    {
        $played_timeshift = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('played_timeshift')->where(['id' => (int) $_REQUEST['hist_id']])->get()->first();
        if (!empty($played_timeshift)) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('played_timeshift', ['length' => \time() - \strtotime($played_timeshift['playtime'])], ['id' => (int) $_REQUEST['hist_id']]);
        }
        return false;
    }
    public function createTasks($chID, $force_storages = array())
    {
        if (empty($force_storages)) {
            return $this->createTask($chID);
        }
        $exist_tasks_raw = $this->getAllTasksForChannel($chID);
        $exist_tasks = [];
        foreach ($exist_tasks_raw as $task) {
            $exist_tasks[$task['storage_name']] = $task;
        }
        $exist_tasks_storages = \array_keys($exist_tasks);
        $need_to_delete = \array_diff($exist_tasks_storages, $force_storages);
        $need_to_add = \array_diff($force_storages, $exist_tasks_storages);
        if (!empty($need_to_delete)) {
            foreach ($need_to_delete as $delete_from_storage) {
                $this->deleteTaskById($exist_tasks[$delete_from_storage]['id']);
            }
        }
        $result = true;
        if (!empty($need_to_add)) {
            foreach ($need_to_add as $add_to_storage) {
                $result = $this->createTask($chID, $add_to_storage) && $result;
            }
        }
        return $result;
    }
    public function createTask($chID, $force_storage = '')
    {
        if (empty($this->storages)) {
            return false;
        }
        $storage_names = \array_keys($this->storages);
        if (!empty($force_storage) && \in_array($force_storage, $storage_names)) {
            $storage_name = $force_storage;
        } else {
            $storage_name = $storage_names[0];
        }
        $exist_task = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tv_archive')->where(['ch_id' => $chID, 'storage_name' => $storage_name])->get()->first();
        if (!empty($exist_task)) {
            return true;
        }
        $task_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('tv_archive', ['ch_id' => $chID, 'storage_name' => $storage_name])->insert_id();
        if (!$task_id) {
            return false;
        }
        if (!empty($force_storage) && \array_key_exists($force_storage, $this->storages) && ($this->storages[$force_storage]['fake_tv_archive'] == 1 || !empty($this->storages[$force_storage]['dvr_type']) && $this->storages[$force_storage]['dvr_type'] !== 'stalker_dvr')) {
            return true;
        }
        $channel = \Ministra\Lib\Itv::getChannelById($chID);
        if (!empty($channel['tv_archive_type']) && $channel['tv_archive_type'] != 'stalker_dvr') {
            return true;
        }
        if (\preg_match("/(\\S+:\\/\\/\\S+)/", $channel['mc_cmd'], $match)) {
            $cmd = $match[1];
        } else {
            $cmd = $channel['mc_cmd'];
        }
        $task = ['id' => $task_id, 'ch_id' => $channel['id'], 'cmd' => $cmd, 'parts_number' => $channel['tv_archive_duration']];
        return $this->clients[$storage_name]->resource('tv_archive_recorder')->create(['task' => $task]);
    }
    public function getAllTasksForChannel($chID)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tv_archive')->where(['ch_id' => $chID])->get()->all();
    }
    protected function deleteTaskById($task_id)
    {
        $task = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tv_archive')->where(['id' => $task_id])->get()->first();
        if (empty($task)) {
            return true;
        }
        if (\array_key_exists($task['storage_name'], $this->storages) && $this->storages[$task['storage_name']]['fake_tv_archive'] == 0 && (empty($this->storages[$task['storage_name']]['dvr_type']) || $this->storages[$task['storage_name']]['dvr_type'] == 'stalker_dvr')) {
            $this->clients[$task['storage_name']]->resource('tv_archive_recorder')->ids($task['ch_id'])->delete();
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('tv_archive', ['id' => $task_id]);
    }
    public function deleteTasks($chID)
    {
        $channel_tasks = $this->getAllTasksForChannel($chID);
        if (empty($channel_tasks)) {
            return true;
        }
        $result = true;
        foreach ($channel_tasks as $task) {
            $result = $this->deleteTaskById($task['id']) && $result;
        }
        return $result;
    }
    public function deleteTask($chID)
    {
        $task = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tv_archive')->where(['ch_id' => $chID])->get()->first();
        if (empty($task)) {
            return true;
        }
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('tv_archive', ['ch_id' => $chID]);
        if (\array_key_exists($task['storage_name'], $this->storages) && ($this->storages[$task['storage_name']]['fake_tv_archive'] == 1 || !empty($this->storages[$task['storage_name']]['dvr_type']) && $this->storages[$task['storage_name']]['dvr_type'] !== 'stalker_dvr')) {
            return true;
        }
        return $this->clients[$task['storage_name']]->resource('tv_archive_recorder')->ids($chID)->delete();
    }
    public function getAllTasksAssoc($storage_name = null)
    {
        $tasks = $this->getAllTasks($storage_name);
        $result = [];
        foreach ($tasks as $task) {
            $result[$task['ch_id']] = $task;
        }
        return $result;
    }
    public function getAllTasks($storage_name = null, $not_fake = false)
    {
        if ($storage_name) {
            $where = ['storage_name' => $storage_name];
        } else {
            $where = [];
        }
        $fake_storages = [];
        if ($not_fake) {
            foreach ($this->storages as $storage_name => $storage) {
                if ($storage['fake_tv_archive'] == 1) {
                    $fake_storages[] = $storage_name;
                }
            }
        }
        $tasks = [];
        $raw_tasks = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('tv_archive.id as id, itv.id as ch_id, itv.mc_cmd as cmd, ' . 'itv.tv_archive_duration as parts_number')->from('tv_archive')->join('itv', 'itv.id', 'tv_archive.ch_id', 'LEFT')->where($where);
        if (!empty($fake_storages)) {
            $raw_tasks = $raw_tasks->in('storage_name', $fake_storages, true);
        }
        $raw_tasks = $raw_tasks->get()->all();
        foreach ($raw_tasks as $task) {
            if (\preg_match("/(\\S+:\\/\\/\\S+)/", $task['cmd'], $match)) {
                $task['cmd'] = $match[1];
            }
            $task['ch_id'] = (int) $task['ch_id'];
            $tasks[] = $task;
        }
        return $tasks;
    }
    public function updateStartTime($chID, $time)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('tv_archive', ['start_time' => \date('Y-m-d H:i:s', $time)], ['ch_id' => (int) $chID]);
    }
    public function updateEndTime($chID, $time)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('tv_archive', ['end_time' => \date('Y-m-d H:i:s', $time)], ['ch_id' => (int) $chID]);
    }
    protected function getAllActiveStorages()
    {
        $storages = [];
        $data = $this->db->from('storages')->where(['status' => 1, 'for_records' => 1])->where([' stream_server_type' => null, 'stream_server_type' => ''], 'OR ')->get()->all();
        foreach ($data as $idx => $storage) {
            $storages[$storage['storage_name']] = $storage;
            $storages[$storage['storage_name']]['load'] = $this->getStorageLoad($storage);
        }
        $storages = $this->sortByLoad($storages);
        return $storages;
    }
    protected function getMediaName()
    {
        return $this->media_id;
    }
    private function getTaskByChId($chID)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tv_archive')->where(['ch_id' => $chID])->get()->first();
    }
}
