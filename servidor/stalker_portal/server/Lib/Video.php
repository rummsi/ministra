<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class Video
{
    public static function getVideoByFileId($file_id)
    {
        $videoID = (int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_series_files')->where(['id' => (int) $file_id])->get()->first('video_id');
        if (!$videoID) {
            return;
        }
        return self::getById($videoID);
    }
    public static function getById($id)
    {
        $id = (int) $id;
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video')->where(['id' => $id])->get()->first();
    }
    public static function getFileById($file_id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_series_files')->where(['id' => (int) $file_id])->get()->first();
    }
    public static function getSeasonById($season_id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_season')->where(['id' => (int) $season_id])->get()->first();
    }
    public static function getEpisodeById($episode_id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_season_series')->where(['id' => (int) $episode_id])->get()->first();
    }
    public static function getEpisodesBySeasonId($season_id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_season_series')->where(['season_id' => (int) $season_id])->get()->all();
    }
    public static function getQualityById($quality_id, $for_api = false)
    {
        $qualities = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('quality')->orderby('width')->get()->all();
        foreach ($qualities as $quality) {
            if ($quality['id'] == $quality_id) {
                return ['id' => (int) $quality['id'], 'code' => $quality['num_title'], 'name' => \_($quality['text_title']), 'width' => (int) $quality['width']];
            }
        }
    }
    public static function getQualityMap()
    {
        $qualities = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('quality')->orderby('width')->get()->all();
        $map = [];
        foreach ($qualities as $quality) {
            $map[$quality['id']] = $quality;
        }
        return $map;
    }
    public static function switchOnById($id, $check_status = false)
    {
        $where = ['id' => (int) $id];
        if ($check_status) {
            $where['status'] = 1;
        }
        if ((int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('video', ['accessed' => 1, 'added' => 'NOW()'], $where)->total_rows()) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('updated_places', ['vclub' => 1]);
            self::log($id, 'on');
            self::disableForHDDevices($id);
        }
    }
    public static function log($videoID, $text, $moderator_id = null)
    {
        if ($moderator_id === null) {
            $moderator_id = $_SESSION['uid'];
        }
        $video = self::getById($videoID);
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('video_log', ['action' => $text, 'video_id' => $videoID, 'video_name' => $video['name'], 'moderator_id' => $moderator_id, 'actiontime' => 'NOW()'])->insert_id();
    }
    private static function disableForHDDevices($id)
    {
        $id = (int) $id;
        return self::setDisableForHDDevices($id, 1);
    }
    private static function setDisableForHDDevices($id, $val)
    {
        $id = (int) $id;
        $val = (int) $val;
        $video = self::getById($id);
        if ($video['hd']) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('video', ['disable_for_hd_devices' => 1], ['name' => $video['name'], 'o_name' => $video['o_name'], 'director' => $video['director'], 'year' => $video['year'], 'hd' => 0]);
        }
        return true;
    }
    public static function switchOffById($id)
    {
        $id = (int) $id;
        if ((int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('video', ['accessed' => 0, 'added' => 'NOW()'], ['id' => $id])->total_rows()) {
            self::log($id, 'off');
            self::enableForHDDevices($id);
        }
    }
    private static function enableForHDDevices($id)
    {
        $id = (int) $id;
        return self::setDisableForHDDevices($id, 0);
    }
    public static function getNotEnded()
    {
        $raw = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('vclub_not_ended')->where(['uid' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id])->get()->all();
        $not_ended = [];
        foreach ($raw as $video) {
            $not_ended[$video['video_id']] = $video;
        }
        return $not_ended;
    }
    public static function getServices()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('id, name')->from('video')->orderby('name')->get()->all();
    }
    public static function isNotEndedHistoryEnabled()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('watched_settings')->get()->first('enable_not_ended') == 1;
    }
    public static function isWatchedHistoryEnabled()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('watched_settings')->get()->first('enable_watched') == 1;
    }
    public static function getWatchedHistorySize()
    {
        return (int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('watched_settings')->get()->first('not_ended_history_size');
    }
    public function getRawAll($is_tv_series = null)
    {
        $user = \Ministra\Lib\User::getInstance();
        $all_users_video_ids = $user->getServicesByType('video');
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select(['*', 'IF(`rating_imdb` >= `rating_kinopoisk`, `rating_imdb`, `rating_kinopoisk`) as rating'])->from('video')->where(['status' => 1, 'accessed' => 1]);
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans') && $all_users_video_ids != 'all') {
            $result->in('video.id', $all_users_video_ids);
        }
        if (null !== $is_tv_series) {
            if ($is_tv_series) {
                $result->where(['is_series' => (int) $is_tv_series, '(`series` <> "a:0:{}" AND NOT ISNULL(`series`) AND `series`<> "") AND 1=' => 1], ' OR ');
            } else {
                $result->where(['is_series' => (int) $is_tv_series, '(`series` = "a:0:{}" OR ISNULL(`series`) OR `series` = "") AND 1=' => 1]);
            }
        }
        return $result;
    }
    public function filterList($list)
    {
        for ($i = 0; $i < \count($list); ++$i) {
            $list[$i]['name'] = \sprintf(\_('video_name_format'), $list[$i]['name'], $list[$i]['o_name']);
            $list[$i]['genres'] = \implode(', ', \array_map(function ($item) {
                return \_($item);
            }, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('cat_genre')->in('id', [$list[$i]['cat_genre_id_1'], $list[$i]['cat_genre_id_2'], $list[$i]['cat_genre_id_3'], $list[$i]['cat_genre_id_4']])->get()->all('title')));
            $list[$i]['genres_ids'] = [];
            for ($j = 1; $j <= 4; ++$j) {
                if ($list[$i]['cat_genre_id_' . $j] > 0) {
                    $list[$i]['genres_ids'][] = (int) $list[$i]['cat_genre_id_' . $j];
                }
            }
            $list[$i]['cover'] = self::getCoverUrl($list[$i]['id']);
            $list[$i]['screenshots'] = self::getScreenshotsUrls($list[$i]['id']);
        }
        return $list;
    }
    public static function getCoverUrl($videoID)
    {
        $cover = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('screenshots')->where(['media_id' => (int) $videoID, 'video_episodes' => 0])->get()->first();
        if (empty($cover)) {
            return false;
        }
        if (!($extension = \pathinfo($cover['name'], PATHINFO_EXTENSION))) {
            $extension = 'jpg';
        }
        return is_available_file(join_paths(PROJECT_PATH, '/../../'), '/' . join_paths(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('screenshots_url'), \ceil($cover['id'] / 100)), $cover['id'], $extension);
    }
    public static function getScreenshotsUrls($videoID)
    {
        $screenshots = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('screenshots')->where(['media_id' => (int) $videoID, 'video_episodes!=' => 0])->get()->all();
        if (empty($screenshots)) {
            return [];
        }
        $urls = \array_values(\array_filter(\array_map(function ($screenshot) {
            if (!($extension = \pathinfo($screenshot['name'], PATHINFO_EXTENSION))) {
                $extension = 'jpg';
            }
            return is_available_file(join_paths(PROJECT_PATH, '/../../'), '/' . join_paths(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('screenshots_url'), \ceil($screenshot['id'] / 100)), $screenshot['id'] . '_' . $screenshot['video_episodes'], $extension);
        }, $screenshots)));
        return $urls;
    }
    public function setLocale($language)
    {
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->H545968ec0047813afbe3121bb5b6c9a5($language);
    }
    public function getFavorites()
    {
    }
}
