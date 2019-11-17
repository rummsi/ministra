<?php

namespace Ministra\Lib;

use Exception;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Y5e6e40f383d7e1d4e0a0b052a8423153 as o27172a8327f94d12ac34e9364569b4a9;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class Vod extends \Ministra\Lib\AjaxResponse implements \Ministra\Lib\StbApi\Vod
{
    private static $instance = null;
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function createLink()
    {
        if (\preg_match("/\\/media\\/(\\d+).mpg(.*)/", $_REQUEST['cmd'], $tmp_arr)) {
            $file_id = 0;
            $media_id = $tmp_arr[1];
        } elseif (\preg_match("/\\/media\\/file_(\\d+).mpg(.*)/", $_REQUEST['cmd'], $tmp_arr)) {
            $file_id = $tmp_arr[1];
            $file = \Ministra\Lib\Video::getFileById($file_id);
            if (!empty($file)) {
                $media_id = $file['video_id'];
            } else {
                $media_id = 0;
            }
            if (empty($_REQUEST['series'])) {
                $series_id = $file['series_id'];
            } else {
                $season_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_season_series')->where(['id' => $file['series_id']])->get()->first('season_id');
                $episode = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_season_series')->where(['season_id' => $season_id, 'series_number' => (int) $_REQUEST['series']])->get()->first();
                $series_id = $episode['id'];
                if ($file['series_id'] != $episode['id']) {
                    $file_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_series_files')->where(['series_id' => $episode['id']])->get()->first('id');
                }
            }
        }
        if ($file) {
            $subtitles = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_series_files')->where(['video_id' => $file['video_id'], 'series_id' => $series_id, 'file_type' => 'sub'])->get()->all();
            $subtitles = \array_filter(\array_map(function ($subtitle) {
                $languages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($subtitle['languages']);
                if ($languages && \is_array($languages) && \count($languages) > 0) {
                    $lang = $languages[0];
                } else {
                    $lang = '';
                }
                return !empty($subtitle['url']) ? ['file' => $subtitle['url'], 'lang' => $lang] : false;
            }, $subtitles));
        } else {
            $subtitles = [];
        }
        $params = $tmp_arr[2];
        $forced_storage = $_REQUEST['forced_storage'];
        $disable_ad = $_REQUEST['disable_ad'];
        $link = $this->getLinkByVideoId($media_id, (int) $_REQUEST['series'], $forced_storage, $file_id);
        if (!empty($link['subtitles'])) {
            $subtitles = \array_merge($subtitles, $link['subtitles']);
        }
        $link['subtitles'] = $subtitles;
        if ($_REQUEST['download']) {
            if (\preg_match('/\\.(\\w*)$/', $link['cmd'], $match)) {
                $extension = $match[1];
            }
            $downloads = new \Ministra\Lib\Downloads();
            $link['cmd'] = $downloads->createDownloadLink('vclub', $media_id, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id, (int) $_REQUEST['series']) . (isset($extension) ? '&ext=.' . $extension : '');
        } else {
            $link['cmd'] = $link['cmd'] . $params;
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans')) {
            $user = \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
            $options = $user->getServicesByType('option');
            if ($options && (\array_search('disable_vclub_ad', $options) !== false || \array_search('disable_ad', $options) !== false)) {
                $disable_ad = true;
            }
        }
        $moderator = $this->db->from('moderators')->where(['mac' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->mac])->use_caching()->get()->first();
        if (!$disable_ad) {
            $disable_ad = !empty($moderator) && $moderator['status'] == 1 && $moderator['disable_vclub_ad'] == 1 || !empty($_REQUEST['download']);
        }
        $vclub_ad = new \Ministra\Lib\VclubAdvertising();
        $advertising = new \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Y5e6e40f383d7e1d4e0a0b052a8423153();
        $advertising->F5a921075b27166ca67d420421e415c78(isset($_COOKIE['adid']) ? $_COOKIE['adid'] : '');
        $campaigns = $advertising->d5a029fa3f44597a7bd0107b4e74bbdb(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id, 102);
        if (!$disable_ad && empty($link['error'])) {
            $playlist = [];
            $video = \Ministra\Lib\Video::getById($media_id);
            if (\is_array($campaigns) && \count($campaigns) > 0) {
                foreach ($campaigns as $campaign) {
                    if (!empty($campaign['campaign']['places'][102])) {
                        $playlist[] = ['id' => $campaign['campaign']['id'], 'media_type' => 'advert', 'cmd' => 'ffmpeg ' . $campaign['ad'], 'is_advert' => true, 'ad_tracking' => $campaign['tracking'], 'ad_must_watch' => $campaign['skip_after'] == 0 ? 'all' : $campaign['skip_after'] . 's'];
                    }
                }
                if (\count($playlist)) {
                    $playlist[] = $link;
                    $link = $playlist;
                }
            } else {
                $picked_ad = $vclub_ad->getOneWeightedRandom($video['category_id']);
                if (!empty($picked_ad)) {
                    if (isset($file) && $file['protocol'] != 'custom') {
                        $link['cmd'] = $_REQUEST['cmd'];
                    }
                    $link = [['id' => 0, 'ad_id' => $picked_ad['id'], 'ad_must_watch' => $picked_ad['must_watch'], 'media_type' => 'vclub_ad', 'cmd' => $picked_ad['url'], 'subtitles' => $subtitles], $link];
                }
            }
        }
        \var_dump($link);
        return $link;
    }
    public function getLinkByVideoId($video_id, $series = 0, $forced_storage = '', $file_id = 0)
    {
        $video_id = (int) $video_id;
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans')) {
            $user = \Ministra\Lib\User::getInstance($this->stb->id);
            $all_user_video_ids = $user->getServicesByType('video', 'single');
            if ($all_user_video_ids === null) {
                $all_user_video_ids = [];
            }
            if ($all_user_video_ids != 'all') {
                $all_user_video_ids = \array_flip($all_user_video_ids);
            }
            $all_user_rented_video_ids = $user->getAllRentedVideo();
            if ((\array_key_exists($video_id, $all_user_video_ids) || $all_user_video_ids == 'all') && !\array_key_exists($video_id, $all_user_rented_video_ids)) {
                return ['id' => $video_id, 'error' => 'access_denied'];
            }
            $video = \Ministra\Lib\Video::getById($video_id);
            if (!empty($video['rtsp_url']) && !$file_id) {
                return ['id' => $video_id, 'cmd' => $this->changeSeriesOnCustomURL($video['rtsp_url'], $series)];
            }
        }
        $master = new \Ministra\Lib\VideoMaster();
        try {
            $res = $master->play($video_id, (int) $series, true, $forced_storage, $file_id);
            $res['cmd'] = $this->changeSeriesOnCustomURL($res['cmd'], $series);
            $file = \Ministra\Lib\Video::getFileById($file_id);
            $res['cmd'] = $this->handleTmpLink($res['cmd'], $file);
        } catch (\Exception $e) {
            \trigger_error($e->getMessage());
        }
        return $res;
    }
    public function changeSeriesOnCustomURL($url = '', $series = 1)
    {
        $tmp_arr = [];
        if ($series < 1) {
            $series = 1;
        }
        if (\preg_match("/(s\\d+e)(\\d+).*\$/i", $url, $tmp_arr)) {
            $search_str = $tmp_arr[1] . $tmp_arr[2];
            $replace_str = $tmp_arr[1] . \str_pad($series, 2, '0', STR_PAD_LEFT);
            $url = \str_replace($search_str, $replace_str, $url);
        }
        return $url;
    }
    private function handleTmpLink($url, $file)
    {
        if ($file['tmp_link_type'] == 'flussonic') {
            $url .= (\strpos($url, '?') ? '&' : '?') . 'token=' . \Ministra\Lib\Master::createTemporaryLink($this->stb->id);
        } elseif ($file['tmp_link_type'] == 'nginx') {
            $secret = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('nginx_secure_link_secret');
            if (\preg_match('/http(s)?:\\/\\/([^\\/]+)\\/(.+)$/', $url, $match)) {
                $uri = '/' . $match[3];
            } else {
                $uri = '';
            }
            $remote_addr = $this->stb->ip;
            $expire = \time() + \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('vclub_nginx_tmp_link_ttl', 7200);
            $hash = \base64_encode(\md5($secret . $uri . $remote_addr . $expire, true));
            $hash = \strtr($hash, '+/', '-_');
            $hash = \str_replace('=', '', $hash);
            $url .= (\strpos($url, '?') ? '&' : '?') . 'st=' . $hash . '&e=' . $expire;
        } elseif ($file['tmp_link_type'] == 'wowza') {
            $url .= (\strpos($url, '?') ? '&' : '?') . 'token=' . \Ministra\Lib\Master::createTemporaryLink('1');
        } elseif ($file['tmp_link_type'] == 'nimble') {
            $url .= (\strpos($url, '?') ? '&' : '?') . \Ministra\Lib\Itv::getNimbleHttpAuthToken($url, 'NIMBLE_VIDEO_VALID_MINUTES');
        } elseif ($file['tmp_link_type'] == 'edgecast_auth') {
            $url .= (\strpos($url, '?') ? '&' : '?') . \Ministra\Lib\Itv::getEdgeCastAuthToken('EDGECAST_VIDEO_SECURITY_TOKEN_TTL');
        } elseif ($file['tmp_link_type'] == 'akamai_auth') {
            $url .= (\strpos($url, '?') ? '&' : '?') . \Ministra\Lib\Itv::getAkamaiToken('AKAMAI_VIDEO_SECURITY_TOKEN_TTL');
        } elseif ($file['tmp_link_type'] == 'wowza_securetoken') {
            $url .= (\strpos($url, '?') ? '&' : '?') . \Ministra\Lib\Itv::getWowzaSecureToken($url, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('wowza_securetoken_vod_endtime', 0));
        }
        return $url;
    }
    public function delLink()
    {
        $item = $_REQUEST['item'];
        if (\preg_match("/\\/(\\w+)\$/", $item, $tmp_arr)) {
            $key = $tmp_arr[1];
            \var_dump($tmp_arr, \strlen($key));
            if (\strlen($key) != 32) {
                return false;
            }
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance()->del($key);
        }
        return false;
    }
    public function setPlayed($video_id = null, $storage_id = null)
    {
        if ($video_id === null) {
            $video_id = @(int) $_REQUEST['video_id'];
        }
        if ($storage_id === null) {
            $storage_id = (int) $_REQUEST['storage_id'];
        }
        if (\date('j') <= 15) {
            $field_name = 'count_first_0_5';
        } else {
            $field_name = 'count_second_0_5';
        }
        $video = $this->db->from('video')->where(['id' => $video_id])->get()->first();
        $this->db->update('video', [$field_name => $video[$field_name] + 1, 'count' => $video['count'] + 1, 'last_played' => 'NOW()'], ['id' => $video_id]);
        $this->db->insert('played_video', ['video_id' => $video_id, 'uid' => $this->stb->id, 'storage' => $storage_id, 'playtime' => 'NOW()']);
        $this->db->update('users', ['time_last_play_video' => 'NOW()'], ['id' => $this->stb->id]);
        $today_record = $this->db->from('daily_played_video')->where(['date' => \date('Y-m-d')])->get()->first();
        if (empty($today_record)) {
            $this->db->insert('daily_played_video', ['count' => 1, 'date' => \date('Y-m-d')]);
        } else {
            $this->db->update('daily_played_video', ['count' => $today_record['count'] + 1, 'date' => \date('Y-m-d')], ['id' => $today_record['id']]);
        }
        $played_video = $this->db->from('user_played_movies')->where(['uid' => $this->stb->id, 'video_id' => $video_id])->get()->all();
        if (empty($played_video)) {
            $this->db->insert('user_played_movies', ['uid' => $this->stb->id, 'video_id' => $video_id, 'watched' => 1, 'playtime' => 'NOW()']);
        } else {
            $this->db->update('user_played_movies', ['playtime' => 'NOW()', 'watched' => 1], ['uid' => $this->stb->id, 'video_id' => $video_id]);
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tariff_plans', false)) {
            $user = \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
            $package = $user->getPackageByVideoId($video['id']);
            if (!empty($package) && $package['service_type'] == 'single') {
                $video_rent_history = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_rent_history')->where(['video_id' => $video['id'], 'uid' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id])->orderby('rent_date', 'DESC')->get()->first();
                if (!empty($video_rent_history)) {
                    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('video_rent_history', ['watched' => $video_rent_history['watched'] + 1], ['id' => $video_rent_history['id']]);
                }
            }
        }
        return true;
    }
    public function setFav()
    {
        $new_id = (int) $_REQUEST['video_id'];
        $favorites = $this->getFav();
        if ($favorites === null) {
            $favorites = [$new_id];
        } else {
            $favorites[] = $new_id;
        }
        return $this->saveFav($favorites, $this->stb->id);
    }
    public function getFav($uid = null)
    {
        if (!$uid) {
            $uid = $this->stb->id;
        }
        return $this->getFavByUid($uid);
    }
    public function getFavByUid($uid)
    {
        $uid = (int) $uid;
        $fav_video_arr = $this->db->from('fav_vclub')->where(['uid' => $uid])->get()->first();
        if ($fav_video_arr === null) {
            return;
        }
        if (empty($fav_video_arr)) {
            return [];
        }
        $fav_video = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($fav_video_arr['fav_video']);
        if (!\is_array($fav_video)) {
            $fav_video = [];
        }
        return $fav_video;
    }
    public function saveFav(array $fav_array, $uid)
    {
        if (empty($uid)) {
            return false;
        }
        $fav_videos_str = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($fav_array);
        $fav_video = $this->getFav($uid);
        if ($fav_video === null) {
            return $this->db->insert('fav_vclub', ['uid' => $uid, 'fav_video' => $fav_videos_str, 'addtime' => 'NOW()'])->insert_id();
        }
        return $this->db->update('fav_vclub', ['fav_video' => $fav_videos_str, 'edittime' => 'NOW()'], ['uid' => $uid])->result();
    }
    public function delFav()
    {
        $del_id = (int) $_REQUEST['video_id'];
        $fav_video = $this->getFav();
        if (\is_array($fav_video)) {
            if (\in_array($del_id, $fav_video)) {
                unset($fav_video[\array_search($del_id, $fav_video)]);
                $fav_video_s = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($fav_video);
                $this->db->update('fav_vclub', ['fav_video' => $fav_video_s, 'edittime' => 'NOW()'], ['uid' => $this->stb->id]);
            }
        }
        return true;
    }
    public function setEnded()
    {
        $video_id = (int) $_REQUEST['video_id'];
        $not_ended = $this->db->from('vclub_not_ended')->where(['uid' => $this->stb->id, 'video_id' => $video_id])->get()->first();
        if (!empty($not_ended)) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('vclub_not_ended', ['uid' => $this->stb->id, 'video_id' => $video_id])->result();
        }
        return true;
    }
    public function setNotEnded()
    {
        $video_id = (int) $_REQUEST['video_id'];
        $series = (int) $_REQUEST['series'];
        $end_time = (int) $_REQUEST['end_time'];
        $file_id = (int) $_REQUEST['file_id'];
        $not_ended = $this->db->from('vclub_not_ended')->where(['uid' => $this->stb->id, 'video_id' => $video_id])->get()->first();
        if (empty($not_ended)) {
            $this->db->insert('vclub_not_ended', ['uid' => $this->stb->id, 'video_id' => $video_id, 'series' => $series, 'end_time' => $end_time, 'file_id' => $file_id, 'added' => 'NOW()']);
        } else {
            $this->db->update('vclub_not_ended', ['series' => $series, 'end_time' => $end_time, 'file_id' => $file_id, 'added' => 'NOW()'], ['uid' => $this->stb->id, 'video_id' => $video_id]);
        }
        return true;
    }
    public function getOrderedList()
    {
        $movie_id = isset($_REQUEST['movie_id']) ? (int) $_REQUEST['movie_id'] : 0;
        $season_id = isset($_REQUEST['season_id']) ? (int) $_REQUEST['season_id'] : 0;
        $episode_id = isset($_REQUEST['episode_id']) ? (int) $_REQUEST['episode_id'] : 0;
        if (!$movie_id && !$season_id && !$episode_id) {
            return $this->getMoviesList();
        } elseif ($movie_id && !$season_id && !$episode_id) {
            $movie = \Ministra\Lib\Video::getById($movie_id);
            if ($movie['is_series']) {
                return $this->getSeasonsList($movie_id);
            }
            return $this->getFilesList($movie_id);
        } elseif ($movie_id && $season_id && !$episode_id) {
            return $this->getEpisodesList($season_id);
        } elseif ($movie_id && $season_id && $episode_id) {
            return $this->getFilesList($movie_id, $episode_id);
        }
    }
    public function getMoviesList()
    {
        $fav = $this->getFav();
        $ls = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('ls');
        if ($ls) {
            $ids_on_ls = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['ls' => $ls])->get()->all('id');
        } else {
            $ids_on_ls = [$this->stb->id];
        }
        $user = \Ministra\Lib\User::getInstance($this->stb->id);
        $all_users_video_ids = $user->getServicesByType('video');
        $result = $this->getData();
        if (@$_REQUEST['sortby']) {
            $sortby = $_REQUEST['sortby'];
            if ($sortby == 'name' || $sortby == 'purchased') {
                $result = $result->orderby('video.name');
            } elseif ($sortby == 'added') {
                $result = $result->orderby('video.added', 'DESC');
            } elseif ($sortby == 'top') {
                $result->select('(count_first_0_5+count_second_0_5) as top')->orderby('top', 'DESC');
            } elseif ($sortby == 'last_ended') {
                $result = $result->orderby('vclub_not_ended.added', 'DESC');
            } elseif ($sortby == 'rating') {
                $result = $result->orderby('video.rating_kinopoisk', 'DESC');
            }
        } else {
            $result = $result->orderby('video.name');
        }
        if (!empty($_REQUEST['sortby']) && $_REQUEST['sortby'] == 'purchased' && \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans')) {
            $rented_video = $user->getAllRentedVideo();
            $rented_video_ids = \array_keys($rented_video);
            $result = $result->in('video.id', $rented_video_ids);
        }
        if (@$_REQUEST['fav']) {
            $result = $result->in('video.id', $fav);
        }
        if (@$_REQUEST['hd']) {
            $result = $result->where(['hd' => 1]);
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans') && $all_users_video_ids != 'all') {
            $result = $result->in('video.id', $all_users_video_ids);
        }
        if (@$_REQUEST['not_ended']) {
            $result = $result->from('vclub_not_ended')->select('vclub_not_ended.series as cur_series, vclub_not_ended.end_time as position, ' . 'vclub_not_ended.file_id as not_ended_file_id')->where('video.id=vclub_not_ended.video_id', 'AND ', null, -1)->in('vclub_not_ended.uid', $ids_on_ls);
        }
        $this->setResponseData($result);
        return $this->getResponse('prepareMoviesList');
    }
    private function getData()
    {
        $offset = $this->page * self::MAX_PAGE_ITEMS;
        $where = [];
        if (@$_REQUEST['hd']) {
            $where['hd'] = 1;
        } else {
            $where['hd<='] = 1;
        }
        if (!empty($_REQUEST['category']) && $_REQUEST['category'] == 'coming_soon') {
            $tasks_video = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('moderator_tasks')->where(['ended' => 0, 'media_type' => 2])->get()->all('media_id');
            $scheduled_video = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_on_tasks')->get()->all('video_id');
            $ids = \array_unique(\array_merge($tasks_video, $scheduled_video));
        } elseif (@$_REQUEST['category'] && @$_REQUEST['category'] !== '*') {
            $where['category_id'] = (int) $_REQUEST['category'];
        }
        if (!$this->stb->c6e0d92fc0ec62469764ba74feb893fa()) {
            if (!isset($ids)) {
                $where['accessed'] = 1;
            }
            $where['status'] = 1;
            if ($this->stb->hd) {
                $where['disable_for_hd_devices'] = 0;
            }
        } else {
            $where['status>='] = 1;
        }
        if (@$_REQUEST['years'] && @$_REQUEST['years'] !== '*') {
            $where['year'] = $_REQUEST['years'];
        }
        if ((empty($_REQUEST['category']) || $_REQUEST['category'] == '*') && !\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_adult_movies_in_common_list', true)) {
            $not_in_category_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('media_category')->where(['category_alias' => 'adult', 'censored' => 1], 'OR ')->get()->all('id');
        }
        $like = [];
        if (@$_REQUEST['abc'] && @$_REQUEST['abc'] !== '*') {
            $letter = $_REQUEST['abc'];
            $like = ['video.name' => $letter . '%'];
        }
        $where_genre = [];
        if (@$_REQUEST['genre'] && @$_REQUEST['genre'] !== '*' && $_REQUEST['category'] !== '*') {
            $genre = (int) $_REQUEST['genre'];
            $where_genre['cat_genre_id_1'] = $genre;
            $where_genre['cat_genre_id_2'] = $genre;
            $where_genre['cat_genre_id_3'] = $genre;
            $where_genre['cat_genre_id_4'] = $genre;
        }
        if (@$_REQUEST['category'] == '*' && @$_REQUEST['genre'] !== '*') {
            $genre_title = $this->db->from('cat_genre')->where(['id' => (int) $_REQUEST['genre']])->get()->first('title');
            $genres_ids = $this->db->from('cat_genre')->where(['title' => $genre_title])->get()->all('id');
        }
        $search = [];
        if (!empty($_REQUEST['search'])) {
            $letters = $_REQUEST['search'];
            $search['video.name'] = '%' . $letters . '%';
            $search['o_name'] = '%' . $letters . '%';
            $search['actors'] = '%' . $letters . '%';
            $search['director'] = '%' . $letters . '%';
            $search['year'] = '%' . $letters . '%';
        }
        $data = $this->db->select('video.*, (select group_concat(screenshots.id) from screenshots ' . 'where media_id=video.id) as screenshots')->from('video')->where($where)->where($where_genre, 'OR ');
        if (isset($ids)) {
            $data->in('id', $ids);
        }
        if (isset($not_in_category_ids)) {
            $data->not_in('category_id', $not_in_category_ids);
        }
        if (!empty($genres_ids) && \is_array($genres_ids)) {
            $data = $data->group_in(['cat_genre_id_1' => $genres_ids, 'cat_genre_id_2' => $genres_ids, 'cat_genre_id_3' => $genres_ids, 'cat_genre_id_4' => $genres_ids], 'OR');
        }
        $data = $data->like($like)->like($search, 'OR ')->limit(self::MAX_PAGE_ITEMS, $offset);
        return $data;
    }
    public function getSeasonsList($movie_id)
    {
        $offset = $this->page * self::MAX_PAGE_ITEMS;
        $seasons = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_season')->where(['video_id' => $movie_id])->orderby('season_number');
        $seasons->limit(self::MAX_PAGE_ITEMS, $offset);
        $this->setResponseData($seasons);
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $item = $this->response['data'][$i];
            $this->response['data'][$i]['name'] = \_('Season') . ' ' . $item['season_number'];
            if ($item['season_name']) {
                $this->response['data'][$i]['name'] .= '. ' . $item['season_name'];
            } elseif ($item['season_original_name']) {
                $this->response['data'][$i]['name'] .= '. ' . $item['season_original_name'];
            }
            $this->response['data'][$i]['is_season'] = true;
        }
        if (!empty($_REQUEST['row'])) {
            $this->response['selected_item'] = $_REQUEST['row'] + 1;
            $this->response['cur_page'] = $this->page;
        }
        return $this->response;
    }
    public function getFilesList($movie_id, $episode_id = 0)
    {
        $offset = $this->page * self::MAX_PAGE_ITEMS;
        $files = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_series_files')->where(['video_id' => $movie_id, 'file_type' => 'video', 'accessed' => 1]);
        if ($episode_id) {
            $files->where(['series_id' => $episode_id]);
        }
        $files->limit(self::MAX_PAGE_ITEMS, $offset);
        $this->setResponseData($files);
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans')) {
            $user = \Ministra\Lib\User::getInstance($this->stb->id);
            $for_rent = $user->getServicesByType('video', 'single');
            if ($for_rent === null) {
                $for_rent = [];
            }
            $rented_video = $user->getAllRentedVideo();
            if ($for_rent != 'all') {
                $for_rent = \array_flip($for_rent);
            } else {
                $for_rent = [];
            }
        } else {
            $for_rent = [];
            $rented_video = [];
        }
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $item = $this->response['data'][$i];
            $language_codes = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($item['languages']);
            if (!\is_array($language_codes)) {
                $language_codes = [];
            }
            $languages = \array_map(function ($code) {
                $language = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('languages')->where(['iso_639_code' => $code])->get()->first('name');
                if ($language) {
                    $language = \_($language);
                } else {
                    $language = $code;
                }
                return $language;
            }, $language_codes);
            $quality_map = \Ministra\Lib\Video::getQualityMap();
            if (isset($quality_map[$item['quality']])) {
                $item['quality'] = \_($quality_map[$item['quality']]['text_title']) . ' (' . $quality_map[$item['quality']]['num_title'] . ')';
            }
            if (\array_key_exists($movie_id, $for_rent) || $for_rent == 'all') {
                $this->response['data'][$i]['for_rent'] = 1;
                if (\array_key_exists($movie_id, $rented_video)) {
                    $this->response['data'][$i]['rent_info'] = $rented_video[$movie_id];
                } else {
                    $this->response['data'][$i]['open'] = 0;
                }
            } else {
                $this->response['data'][$i]['for_rent'] = 0;
            }
            $this->response['data'][$i]['name'] = \implode(', ', $languages) . ' / ' . $item['quality'];
            $this->response['data'][$i]['is_file'] = true;
            if (!empty($this->response['data'][$i]['url']) && $this->response['data'][$i]['protocol'] == 'custom' && $this->response['data'][$i]['for_rent'] == 0) {
                $this->response['data'][$i]['cmd'] = $this->response['data'][$i]['url'];
            } else {
                $this->response['data'][$i]['cmd'] = '/media/file_' . $this->response['data'][$i]['id'] . '.mpg';
            }
        }
        if (!empty($_REQUEST['row'])) {
            $this->response['selected_item'] = $_REQUEST['row'] + 1;
            $this->response['cur_page'] = $this->page;
        }
        return $this->response;
    }
    public function getEpisodesList($season_id)
    {
        $offset = $this->page * self::MAX_PAGE_ITEMS;
        $episodes = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('video_season_series.*')->from('video_season_series')->join('video_series_files', ['video_season_series.id' => 'video_series_files.series_id', 'video_series_files.file_type' => '"video"'], null, 'LEFT')->where(['season_id' => $season_id, 'video_series_files.accessed' => 1])->groupby('video_season_series.id')->orderby('series_number');
        $episodes->limit(self::MAX_PAGE_ITEMS, $offset);
        $episodes_nums = clone $episodes;
        $episodes_nums = $episodes_nums->nolimit()->get()->all('series_number');
        $this->setResponseData($episodes);
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $item = $this->response['data'][$i];
            $this->response['data'][$i]['name'] = \_('Episode') . ' ' . $item['series_number'];
            if ($item['series_name']) {
                $this->response['data'][$i]['name'] .= '. ' . $item['series_name'];
            } elseif ($item['series_original_name']) {
                $this->response['data'][$i]['name'] .= '. ' . $item['series_original_name'];
            }
            $this->response['data'][$i]['is_episode'] = true;
            $this->response['data'][$i]['series'] = $episodes_nums;
        }
        if (!empty($_REQUEST['row'])) {
            $this->response['selected_item'] = $_REQUEST['row'] + 1;
            $this->response['cur_page'] = $this->page;
        }
        return $this->response;
    }
    public function getCategories()
    {
        if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_empty_vclub_category', true)) {
            $user = \Ministra\Lib\User::getInstance($this->stb->id);
            $all_users_video_ids = $user->getServicesByType('video');
            $user_categories = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video')->select('category_id')->groupby('category_id');
            if (!$this->stb->c6e0d92fc0ec62469764ba74feb893fa()) {
                $user_categories->where(['accessed' => 1, 'status' => 1]);
                if ($this->stb->hd) {
                    $user_categories->where(['disable_for_hd_devices' => 0]);
                }
            } else {
                $user_categories->where(['status>=' => 1]);
            }
            if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans') && $all_users_video_ids != 'all') {
                $user_categories->in('video.id', $all_users_video_ids);
            }
            $user_categories = $user_categories->get()->all('category_id');
        }
        $categories = $this->db->select('id, category_name as title, category_alias as alias, censored')->from('media_category')->orderby('num');
        if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_empty_vclub_category', true) && isset($user_categories)) {
            $categories->in('id', $user_categories);
        }
        $categories = $categories->get()->all();
        \array_unshift($categories, ['id' => '*', 'title' => $this->all_title, 'alias' => '*']);
        $categories = \array_map(function ($item) {
            $item['title'] = \_($item['title']);
            $item['censored'] = (int) $item['censored'];
            return $item;
        }, $categories);
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_coming_soon_section', false)) {
            $categories[] = ['id' => 'coming_soon', 'title' => \_('coming soon'), 'alias' => 'coming_soon', 'censored' => 0];
        }
        return $categories;
    }
    public function getGenresByCategoryAlias($cat_alias = '')
    {
        if (!$cat_alias) {
            $cat_alias = @$_REQUEST['cat_alias'];
        }
        $where = [];
        if ($cat_alias != '*') {
            $where['category_alias'] = $cat_alias;
        }
        $genres = $this->db->select('id, title')->from('cat_genre')->where($where)->groupby('title')->orderby('title')->get()->all();
        \array_unshift($genres, ['id' => '*', 'title' => '*']);
        $genres = \array_map(function ($item) {
            $item['title'] = \_($item['title']);
            return $item;
        }, $genres);
        return $genres;
    }
    public function getYears()
    {
        $where = ['year>' => '1900'];
        if (@$_REQUEST['category'] && @$_REQUEST['category'] !== '*') {
            $where['category_id'] = $_REQUEST['category'];
        }
        $years = $this->db->select('year as id, year as title')->from('video')->where($where)->groupby('year')->orderby('year')->get()->all();
        \array_unshift($years, ['id' => '*', 'title' => '*']);
        return $years;
    }
    public function getAbc()
    {
        $abc = [];
        foreach ($this->abc as $item) {
            $abc[] = ['id' => $item, 'title' => $item];
        }
        return $abc;
    }
    public function setClaim()
    {
        return $this->setClaimGlobal('vclub');
    }
    public function getUrlByVideoId($video_id, $series = 0, $forced_storage = '', $file_id = 0)
    {
        $video = \Ministra\Lib\Video::getById($video_id);
        if (empty($video)) {
            throw new \Exception('Video not found');
        }
        if (!empty($video['rtsp_url']) && !$file_id) {
            return $video['rtsp_url'];
        }
        $link = $this->getLinkByVideoId($video_id, $series, $forced_storage, $file_id);
        if (empty($link['cmd'])) {
            throw new \Exception('Obtaining url failed');
        }
        if ($file_id) {
            $file = \Ministra\Lib\Video::getFileById($file_id);
            $link['cmd'] = $this->handleTmpLink($link['cmd'], $file);
        }
        if (!empty($link['storage_id'])) {
            $storage = \Ministra\Lib\Master::getStorageById($link['storage_id']);
            if (!empty($storage)) {
                $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
                $cache->set($this->stb->id . '_playback', ['type' => 'video', 'id' => $link['id'], 'storage' => $storage['storage_name'], 'storage_id' => $storage['id']], 0, 10);
            }
        } else {
            $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
            $cache->del($this->stb->id . '_playback');
        }
        return $link['cmd'];
    }
    public function getUrlByFileId($file_id, $forced_storage = '')
    {
        $video = \Ministra\Lib\Video::getVideoByFileId($file_id);
        if (empty($video)) {
            throw new \Exception('Video not found');
        }
        $file = \Ministra\Lib\Video::getFileById($file_id);
        $link = $this->getLinkByFileId($file_id, $forced_storage);
        if (empty($link['cmd'])) {
            throw new \Exception('Obtaining url failed');
        }
        $link['cmd'] = $this->handleTmpLink($link['cmd'], $file);
        if (!empty($link['storage_id'])) {
            $storage = \Ministra\Lib\Master::getStorageById($link['storage_id']);
            if (!empty($storage)) {
                $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
                $cache->set($this->stb->id . '_playback', ['type' => 'video', 'id' => $link['id'], 'storage' => $storage['storage_name']], 0, 10);
            }
        } else {
            $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
            $cache->del($this->stb->id . '_playback');
        }
        return $link['cmd'];
    }
    public function getLinkByFileId($file_id, $forced_storage = '')
    {
        $video = \Ministra\Lib\Video::getVideoByFileId($file_id);
        $video_id = $video['id'];
        $file = \Ministra\Lib\Video::getFileById($file_id);
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans')) {
            $user = \Ministra\Lib\User::getInstance($this->stb->id);
            $all_user_video_ids = $user->getServicesByType('video', 'single');
            if ($all_user_video_ids === null) {
                $all_user_video_ids = [];
            }
            if ($all_user_video_ids != 'all') {
                $all_user_video_ids = \array_flip($all_user_video_ids);
            }
            $all_user_rented_video_ids = $user->getAllRentedVideo();
            if ((\array_key_exists($video_id, $all_user_video_ids) || $all_user_video_ids == 'all') && !\array_key_exists($video_id, $all_user_rented_video_ids)) {
                return ['id' => $video_id, 'error' => 'access_denied'];
            }
            if ($file['protocol'] == 'custom_url' && !empty($file['url'])) {
                return ['id' => $video_id, 'cmd' => $file['url']];
            }
        }
        $master = new \Ministra\Lib\VideoMaster();
        try {
            $res = $master->play($video_id, 0, true, $forced_storage, $file_id);
        } catch (\Exception $e) {
            \trigger_error($e->getMessage());
        }
        return $res;
    }
    public function getMediaCats()
    {
        return $this->db->get('media_category')->all();
    }
    public function setVote()
    {
        if ($_REQUEST['vote'] == 'good') {
            $good = 1;
            $bad = 0;
        } else {
            $good = 0;
            $bad = 1;
        }
        $type = $_REQUEST['type'];
        $this->db->insert('vclub_vote', ['media_id' => (int) $_REQUEST['media_id'], 'uid' => $this->stb->id, 'vote_type' => $type, 'good' => $good, 'bad' => $bad, 'added' => 'NOW()']);
        $video = $this->db->from('video')->where(['id' => (int) $_REQUEST['media_id']])->get()->first();
        $this->db->update('video', ['vote_' . $type . '_good' => $video['vote_' . $type . '_good'] + $good, 'vote_' . $type . '_bad' => $video['vote_' . $type . '_bad'] + $bad], ['id' => (int) $_REQUEST['media_id']]);
        return true;
    }
    public function prepareMoviesList()
    {
        $fav = $this->getFav();
        $not_ended = \Ministra\Lib\Video::getNotEnded();
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans')) {
            $user = \Ministra\Lib\User::getInstance($this->stb->id);
            $for_rent = $user->getServicesByType('video', 'single');
            if ($for_rent === null) {
                $for_rent = [];
            }
            $rented_video = $user->getAllRentedVideo();
            if ($for_rent != 'all') {
                $for_rent = \array_flip($for_rent);
            } else {
                $for_rent = [];
            }
        } else {
            $for_rent = [];
            $rented_video = [];
        }
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $this->response['data'][$i]['is_movie'] = true;
            $this->response['data'][$i]['name'] = \sprintf(\_('video_name_format'), $this->response['data'][$i]['name'], $this->response['data'][$i]['o_name']);
            unset($this->response['data'][$i]['hd']);
            if ($this->response['data'][$i]['censored']) {
                $this->response['data'][$i]['lock'] = 1;
            } else {
                $this->response['data'][$i]['lock'] = 0;
            }
            if ($fav !== null && \in_array($this->response['data'][$i]['id'], $fav)) {
                $this->response['data'][$i]['fav'] = 1;
            } else {
                $this->response['data'][$i]['fav'] = 0;
            }
            if (\array_key_exists($this->response['data'][$i]['id'], $for_rent) || $for_rent == 'all') {
                $this->response['data'][$i]['for_rent'] = 1;
                if (\array_key_exists($this->response['data'][$i]['id'], $rented_video)) {
                    $this->response['data'][$i]['rent_info'] = $rented_video[$this->response['data'][$i]['id']];
                } else {
                    $this->response['data'][$i]['open'] = 0;
                }
            } else {
                $this->response['data'][$i]['for_rent'] = 0;
            }
            $this->response['data'][$i]['has_files'] = (int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_series_files')->where(['video_id' => $this->response['data'][$i]['id']])->count()->get()->counter();
            $this->response['data'][$i]['series'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($this->response['data'][$i]['series']);
            if ($this->response['data'][$i]['has_files']) {
                $this->response['data'][$i]['series'] = [];
            }
            if (!empty($this->response['data'][$i]['series']) && !$this->response['data'][$i]['has_files']) {
                $this->response['data'][$i]['position'] = 0;
            }
            if (!empty($not_ended[$this->response['data'][$i]['id']]) && !empty($this->response['data'][$i]['series']) && !$this->response['data'][$i]['has_files']) {
                $this->response['data'][$i]['cur_series'] = $not_ended[$this->response['data'][$i]['id']]['series'];
            }
            if ($this->response['data'][$i]['screenshots'] === null) {
                $this->response['data'][$i]['screenshots'] = '0';
            }
            $screenshots = \explode(',', $this->response['data'][$i]['screenshots']);
            $this->response['data'][$i]['screenshot_uri'] = $this->getImgUri($screenshots[0]);
            $this->response['data'][$i]['genres_str'] = $this->getGenresStrByItem($this->response['data'][$i]);
            if (!empty($this->response['data'][$i]['rtsp_url']) && $this->response['data'][$i]['for_rent'] == 0) {
                if (!empty($this->response['data'][$i]['series'])) {
                    $this->response['data'][$i]['cmd'] = $this->response['data'][$i]['rtsp_url'] = $this->changeSeriesOnCustomURL($this->response['data'][$i]['rtsp_url'], $this->response['data'][$i]['cur_series']);
                } else {
                    $this->response['data'][$i]['cmd'] = $this->response['data'][$i]['rtsp_url'];
                }
            } else {
                $this->response['data'][$i]['cmd'] = '/media/' . $this->response['data'][$i]['id'] . '.mpg';
            }
            if (@$_REQUEST['sortby'] && @$_REQUEST['sortby'] == 'added') {
                $this->response['data'][$i] = \array_merge($this->response['data'][$i], $this->getAddedArr($this->response['data'][$i]['added']));
            }
            if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_video_low_quality_option', false)) {
                $this->response['data'][$i]['low_quality'] = (int) $this->response['data'][$i]['low_quality'];
            } else {
                $this->response['data'][$i]['low_quality'] = 0;
            }
            $this->response['data'][$i]['rating_imdb'] = \round($this->response['data'][$i]['rating_imdb'], 2);
            $this->response['data'][$i]['rating_kinopoisk'] = \round($this->response['data'][$i]['rating_kinopoisk'], 2);
        }
        if (!empty($_REQUEST['row'])) {
            $this->response['selected_item'] = $_REQUEST['row'] + 1;
            $this->response['cur_page'] = $this->page + 1;
        }
        return $this->response;
    }
    public function getGenresStrByItem($item)
    {
        return \implode(', ', \array_map(function ($item) {
            $item = \_($item);
            $fc = \mb_strtoupper(\mb_substr($item, 0, 1, 'UTF-8'), 'UTF-8');
            $item = $fc . \mb_substr($item, 1, \mb_strlen($item), 'UTF-8');
            return $item;
        }, $this->db->from('cat_genre')->in('id', [$item['cat_genre_id_1'], $item['cat_genre_id_2'], $item['cat_genre_id_3'], $item['cat_genre_id_4']])->get()->all('title')));
    }
    private function getAddedArr($datetime)
    {
        $added_time = \strtotime($datetime);
        $added_arr = [];
        $this_mm = \date('m');
        $this_dd = \date('d');
        $this_yy = \date('Y');
        if ($added_time > \mktime(0, 0, 0, $this_mm, $this_dd, $this_yy)) {
            $added_arr['today'] = \_('today');
        } elseif ($added_time > \mktime(0, 0, 0, $this_mm, $this_dd - 1, $this_yy)) {
            $added_arr['yesterday'] = \_('yesterday');
        } elseif ($added_time > \mktime(0, 0, 0, $this_mm, $this_dd - 7, $this_yy)) {
            $added_arr['week_and_more'] = \_('last week');
        } else {
            $added_arr['week_and_more'] = $this->months[\date('n', $added_time) - 1] . ' ' . \date('Y', $added_time);
        }
        return $added_arr;
    }
}
