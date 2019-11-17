<?php

namespace Ministra\Lib;

use Exception;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
abstract class Master
{
    protected $storages;
    protected $clients;
    protected $stb;
    protected $media_id;
    protected $media_name;
    protected $media_path;
    protected $db;
    protected $media_type;
    protected $media_protocol;
    protected $media_params;
    protected $rtsp_url;
    protected $db_table;
    protected $stb_storages;
    protected $is_file = false;
    private $moderator_storages;
    private $from_cache;
    private $cache_expire_h = 365;
    public function __construct()
    {
        $this->db = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        $this->stb = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance();
        $this->storages = $this->getAllActiveStorages();
        $this->moderator_storages = $this->getModeratorStorages();
        $this->clients = $this->getClients();
        $this->cache_expire_h = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('master_cache_expire');
        $this->stb_storages = $this->getStoragesForStb();
    }
    protected function getAllActiveStorages()
    {
        $storages = [];
        $data = $this->db->from('storages')->where(['status' => 1, 'for_simple_storage' => 1])->get()->all();
        foreach ($data as $idx => $storage) {
            $storages[$storage['storage_name']] = $storage;
        }
        return $storages;
    }
    public function getModeratorStorages()
    {
        $data = $this->db->from('storages')->where(['status' => 1, 'for_moderator' => 1])->get()->all();
        $storages = [];
        foreach ($data as $idx => $storage) {
            $storages[$storage['storage_name']] = $storage;
        }
        return $storages;
    }
    protected function getClients()
    {
        $clients = [];
        $user = \Ministra\Lib\User::getInstance();
        $uid = $user->getId();
        $mac = $user->getMac();
        if ($mac) {
            \Ministra\Lib\RESTClient::$from = $mac;
        } elseif ($uid) {
            \Ministra\Lib\RESTClient::$from = $uid;
        } else {
            \Ministra\Lib\RESTClient::$from = $this->stb->mac;
        }
        \Ministra\Lib\RESTClient::setAccessToken($this->createAccessToken());
        foreach ($this->storages as $name => $storage) {
            $clients[$name] = new \Ministra\Lib\RESTClient('http://' . $storage['storage_ip'] . '/stalker_portal/storage/rest.php?q=');
        }
        return $clients;
    }
    private function createAccessToken()
    {
        $key = \md5(\microtime(1) . \uniqid());
        $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
        $result = $cache->set($key, 'storage', 0, 120);
        return $key;
    }
    public function getStoragesForStb()
    {
        $storages = [];
        $where = ['status' => 1];
        if (!$this->stb->c6e0d92fc0ec62469764ba74feb893fa()) {
            $where['for_moderator'] = 0;
        }
        $data = $this->db->from('storages')->where($where)->get()->all();
        foreach ($data as $idx => $storage) {
            $storages[$storage['storage_name']] = $storage;
        }
        return $storages;
    }
    public static function checkTemporaryLink($key)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance()->get($key);
    }
    public static function delTemporaryLink($key)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance()->del($key);
    }
    public static function getStorageByName($name)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('storages')->where(['storage_name' => $name])->get()->first();
    }
    public static function getStorageById($id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('storages')->where(['id' => $id])->get()->first();
    }
    public static function checkAccessToken($token)
    {
        if (!$token) {
            return false;
        }
        $val = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance()->get($token);
        return $val === 'storage';
    }
    public function play($media_id, $series_num = 0, $from_cache = true, $forced_storage = '', $file_id = 0)
    {
        $this->initMedia($media_id, $file_id);
        $res = ['id' => 0, 'cmd' => '', 'storage_id' => '', 'load' => '', 'error' => ''];
        if (!empty($this->rtsp_url) && (!$file_id || $this->is_file)) {
            $res['id'] = $this->media_id;
            $res['cmd'] = $this->rtsp_url;
            return $res;
        }
        if (!empty($forced_storage)) {
            $from_cache = false;
        }
        $good_storages = $this->getAllGoodStoragesForMedia($this->media_id, $file_id, !$from_cache);
        if (!empty($forced_storage)) {
            if (\array_key_exists($forced_storage, $good_storages)) {
                $good_storages = [$forced_storage => $good_storages[$forced_storage]];
            } else {
                $good_storages = [];
            }
        }
        $default_error = 'nothing_to_play';
        foreach ($good_storages as $name => $storage) {
            if ($storage['load'] < 1) {
                if ($file_id) {
                    $file = \Ministra\Lib\Video::getFileById($file_id);
                    $file = $file['file_name'];
                } elseif ($series_num > 0) {
                    $file = $storage['series_file'][\array_search($series_num, $storage['series'])];
                } else {
                    $file = $storage['first_media'];
                }
                \preg_match("/([\\S\\s]+)\\.([\\S]+)\$/", $file, $arr);
                $ext = $arr[2];
                if ($this->storages[$name]['external'] == 0) {
                    try {
                        $this->clients[$name]->resource($this->media_type)->create(['media_name' => $this->getMediaPath($file, $file_id), 'media_id' => $this->media_id, 'proto' => $this->media_protocol]);
                    } catch (\Exception $exception) {
                        $default_error = 'link_fault';
                        $this->parseException($exception);
                        if ($exception instanceof \Ministra\Lib\RESTClientException && !$exception instanceof \Ministra\Lib\RESTClientRemoteError) {
                            $storage = new \Ministra\Lib\Storage(['name' => $name]);
                            $storage->markAsFailed($exception->getMessage());
                            continue;
                        }
                        if ($this->from_cache) {
                            return $this->play($media_id, $series_num, false, '', $file_id);
                        }
                        continue;
                    }
                    if ($this->media_protocol == 'http' || $this->media_type == 'remote_pvr') {
                        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('nfs_proxy')) {
                            $base_path = 'http://' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('nfs_proxy') . '/media/' . $name . '/' . \Ministra\Lib\RESTClient::$from . '/';
                        } elseif ($this->storages[$name]['stream_server_type'] == 'wowza') {
                            $base_path = 'http://' . $this->storages[$name]['storage_ip'] . ':' . $this->storages[$name]['stream_server_port'] . '/' . $this->storages[$name]['stream_server_app'] . '/_definst_/mp4:' . $this->getMediaPath($file, $file_id) . '/';
                        } elseif ($this->storages[$name]['stream_server_type'] == 'flussonic') {
                            $base_path = 'http://' . $this->storages[$name]['storage_ip'] . ':' . $this->storages[$name]['stream_server_port'] . '/' . $this->storages[$name]['stream_server_app'] . '/' . $this->getMediaPath($file, $file_id) . '/';
                        } else {
                            $base_path = 'http://' . $this->storages[$name]['storage_ip'] . '/media/' . $name . '/' . \Ministra\Lib\RESTClient::$from . '/';
                        }
                    } else {
                        $base_path = '/media/' . $name . '/';
                    }
                    if (\strpos($base_path, 'http://') !== false) {
                        $res['cmd'] = 'ffmpeg ';
                    } else {
                        $res['cmd'] = 'auto ';
                    }
                    if ($this->storages[$name]['stream_server_type'] == 'wowza') {
                        $res['cmd'] .= $base_path . 'playlist.m3u8?token=' . self::createTemporaryLink('1');
                    } elseif ($this->storages[$name]['stream_server_type'] == 'flussonic') {
                        $res['cmd'] .= $base_path . 'index.m3u8?token=' . self::createTemporaryLink($this->stb->id);
                    } else {
                        $res['cmd'] .= $base_path . $this->media_id . '.' . $ext;
                        $secret = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('nginx_secure_link_secret');
                        if (\preg_match('/http(s)?:\\/\\/([^\\/]+)\\/(.+)$/', $res['cmd'], $match)) {
                            $uri = '/' . $match[3];
                        } else {
                            $uri = '';
                        }
                        $remote_addr = $this->stb->ip;
                        $expire = \time() + \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('vclub_nginx_tmp_link_ttl', 7200);
                        $hash = \base64_encode(\md5($secret . $uri . $remote_addr . $expire, true));
                        $hash = \strtr($hash, '+/', '-_');
                        $hash = \str_replace('=', '', $hash);
                        $res['cmd'] .= '?st=' . $hash . '&e=' . $expire;
                    }
                    $file_info = \array_filter($storage['files'], function ($info) use($file) {
                        $info_name = \explode('/', $info['name']);
                        return \end($info_name) == $file;
                    });
                    if (empty($file_info) && !empty($series_num) && !empty($storage['tv_series']) && !empty($file_id)) {
                        $file_rec = \Ministra\Lib\Video::getFileById($file_id);
                        if (!empty($file_rec['series_id'])) {
                            $episode = \Ministra\Lib\Video::getEpisodeById($file_rec['series_id']);
                            $season = \Ministra\Lib\Video::getSeasonById($episode['season_id']);
                            $seasons = $storage['tv_series']['seasons'];
                            $file_info = \array_filter($seasons[$season['season_number']]['episodes'][$episode['series_number']], function ($info) use($file) {
                                $info_name = \explode('/', $info['name']);
                                return \end($info_name) == $file;
                            });
                        }
                    }
                    $file_info = \array_values($file_info);
                    if (!empty($file_info) && !empty($file_info[0]['subtitles'])) {
                        $ip = $this->stb->ip;
                        $res['subtitles'] = \array_map(function ($subtitle) use($base_path, $file, $ip) {
                            $file_base = \substr($file, 0, \strrpos($file, '.'));
                            $lang = \substr($subtitle, \strlen($file_base), \strrpos($subtitle, '.') - \strlen($file_base));
                            if ($lang && ($lang[0] == '_' || $lang[0] == '.')) {
                                $lang = \substr($lang, 1);
                            }
                            $file = $base_path . $subtitle;
                            $secret = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('nginx_secure_link_secret');
                            if (\preg_match('/http(s)?:\\/\\/([^\\/]+)\\/(.+)$/', $file, $match)) {
                                $uri = '/' . $match[3];
                            } else {
                                $uri = '';
                            }
                            $remote_addr = $ip;
                            $expire = \time() + \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('vclub_nginx_tmp_link_ttl', 7200);
                            $hash = \base64_encode(\md5($secret . $uri . $remote_addr . $expire, true));
                            $hash = \strtr($hash, '+/', '-_');
                            $hash = \str_replace('=', '', $hash);
                            return ['file' => $file . '?st=' . $hash . '&e=' . $expire, 'lang' => $lang];
                        }, $file_info[0]['subtitles']);
                    }
                } else {
                    $redirect_url = '/media/' . $this->getMediaPath($file, $file_id);
                    $link_result = self::createTemporaryLink($redirect_url);
                    \var_dump($redirect_url, $link_result);
                    if (!$link_result) {
                        $default_error = 'link_fault';
                        if ($this->from_cache) {
                            return $this->play($media_id, $series_num, false, '', $file_id);
                        }
                        continue;
                    }
                    $res['cmd'] = 'ffmpeg http://' . $this->storages[$name]['storage_ip'] . '/get/' . $link_result;
                    $res['external'] = 1;
                }
                $res['id'] = $this->media_id;
                $res['load'] = $storage['load'];
                $res['storage_id'] = $this->storages[$name]['id'];
                $res['from_cache'] = $this->from_cache;
                return $res;
            }
            $this->incrementStorageDeny($name);
            $res['error'] = 'limit';
            return $res;
        }
        if ($this->from_cache) {
            return $this->play($media_id, $series_num, false, '', $file_id);
        }
        $res['error'] = $default_error;
        return $res;
    }
    private function initMedia($media_id, $file_id)
    {
        if (empty($this->media_id)) {
            $this->media_id = $media_id;
        }
        if (empty($this->media_params)) {
            $this->media_params = $this->getMediaParams($this->media_id, $file_id);
        }
        if (empty($this->media_name)) {
            $this->media_name = $this->getMediaName();
        }
    }
    protected function getMediaParams($media_id, $file_id)
    {
        $media_params = $this->db->from($this->db_table)->where(['id' => $media_id])->get()->first();
        $file = \Ministra\Lib\Video::getFileById($file_id);
        if (!empty($file)) {
            if (!empty($file['url']) && $file['protocol'] != 'http') {
                $this->rtsp_url = $file['url'];
            }
            if (!empty($file['protocol'])) {
                $this->media_protocol = $file['protocol'];
            }
            $this->is_file = true;
        } else {
            if (!empty($media_params['rtsp_url'])) {
                $this->rtsp_url = $media_params['rtsp_url'];
            }
            if (!empty($media_params['protocol'])) {
                $this->media_protocol = $media_params['protocol'];
            }
        }
        return $media_params;
    }
    protected abstract function getMediaName();
    private function getAllGoodStoragesForMedia($media_id, $file_id, $force_net = false)
    {
        $cache = [];
        $this->initMedia($media_id, $file_id);
        if ($this->stb->c6e0d92fc0ec62469764ba74feb893fa()) {
            $good_storages = $this->getAllGoodStoragesForMediaFromNet($this->media_name, $file_id);
            $good_storages = $this->sortByLoad($good_storages);
            return $good_storages;
        }
        if (!$force_net) {
            $cache = $this->getAllGoodStoragesForMediaFromCache();
        }
        if (!empty($cache)) {
            $good_storages = $cache;
            $this->from_cache = true;
        } else {
            $good_storages = $this->getAllGoodStoragesForMediaFromNet($this->media_name, $file_id);
            $this->from_cache = false;
        }
        $good_storages = $this->sortByLoad($good_storages);
        if (\Ministra\Lib\User::isInitialized()) {
            $user_agent = \Ministra\Lib\User::getUserAgent();
            $filtered_good_storages = [];
            foreach ($good_storages as $storage_name => $storage) {
                $user_agent_filter = $this->storages[$storage_name]['user_agent_filter'];
                if (!empty($user_agent_filter) && (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::isValidIp($user_agent_filter) || \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::af4b8c53e808fb04e8b4b2200c4876a0($user_agent_filter))) {
                    $user_agent_filter = \preg_quote($user_agent_filter);
                }
                if ($user_agent_filter == '' || \preg_match('/' . $user_agent_filter . '/', $user_agent)) {
                    $filtered_good_storages[$storage_name] = $storage;
                }
            }
            $good_storages = $filtered_good_storages;
        }
        return $good_storages;
    }
    public function getAllGoodStoragesForMediaFromNet($media_id, $file_id, $force_moderator = false)
    {
        $this->initMedia($media_id, $file_id);
        $good_storages = [];
        if ($this->stb->c6e0d92fc0ec62469764ba74feb893fa() || $force_moderator) {
            $storages = $this->storages;
        } else {
            $storages = \array_diff_key($this->storages, $this->moderator_storages);
        }
        foreach ($storages as $name => $storage) {
            $raw = $this->checkMediaDir($name, $this->media_name);
            if (!$raw) {
                continue;
            }
            if (\count($raw['files']) > 0) {
                $raw['first_media'] = $raw['files'][0]['name'];
                if (!$file_id) {
                    $this->saveSeries($raw['series']);
                }
                $raw['load'] = $this->getStorageLoad($storage);
                $raw['for_moderator'] = $storage['for_moderator'];
                $good_storages[$name] = $raw;
            } elseif (!empty($raw['tv_series'])) {
                $raw['load'] = $this->getStorageLoad($storage);
                $raw['for_moderator'] = $storage['for_moderator'];
                $good_storages[$name] = $raw;
            }
            $raw['tv_series'] = isset($storage['tv_series']) ? $storage['tv_series'] : [];
        }
        $this->checkMD5Sum($good_storages);
        if (!$this->stb->c6e0d92fc0ec62469764ba74feb893fa()) {
            $this->setStorageCache($good_storages);
        }
        if (\method_exists($this, 'setStatus')) {
            $status = (int) $good_storages;
            if ($status == 1 && !\array_diff_key($good_storages, $this->moderator_storages)) {
                $status = 3;
            }
            \call_user_func_array([$this, 'setStatus'], [$status, $file_id]);
        }
        return $good_storages;
    }
    protected function checkMediaDir($storage_name, $media_name)
    {
        try {
            return $this->clients[$storage_name]->resource($this->media_type)->ids($media_name)->get();
        } catch (\Exception $exception) {
            $this->parseException($exception);
            return false;
        }
    }
    protected function parseException($exception)
    {
        echo $exception->getMessage() . "\n" . $exception->getTraceAsString();
        $this->addToLog($exception->getMessage());
    }
    private function addToLog($txt)
    {
        $this->db->insert('master_log', ['log_txt' => \trim($txt), 'added' => 'NOW()']);
    }
    protected function saveSeries($series_arr)
    {
        return true;
    }
    protected function getStorageLoad($storage)
    {
        if ($storage['max_online'] > 0) {
            return $this->getStorageOnline($storage['storage_name']) / $storage['max_online'];
        }
        return 1;
    }
    protected function getStorageOnline($storage_name)
    {
        $vclub_sd_sessions = $this->db->select('count(*) as sd_online')->from('users')->where(['now_playing_type' => 2, 'hd_content' => 0, 'storage_name' => $storage_name, 'keep_alive>' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2)])->get()->first('sd_online');
        $vclub_hd_sessions = $this->db->select('count(*) as hd_online')->from('users')->where(['now_playing_type' => 2, 'hd_content' => 1, 'storage_name' => $storage_name, 'keep_alive>' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2)])->get()->first('hd_online');
        $pvr_rec_sessions = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('rec_files')->where(['storage_name' => $storage_name, 'ended' => 0])->get()->count();
        $archive_rec_sessions = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tv_archive')->where(['storage_name' => $storage_name])->get()->count();
        $archive_sessions = $this->db->select('count(*) as archive_sessions')->from('users')->where(['now_playing_type' => 11, 'hd_content' => 0, 'storage_name' => $storage_name, 'keep_alive>' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2)])->get()->first('archive_sessions');
        return $vclub_sd_sessions + 3 * $vclub_hd_sessions + $pvr_rec_sessions + $archive_rec_sessions + $archive_sessions;
    }
    private function checkMD5Sum($storages_from_net)
    {
        $storages_from_cache = $this->getAllGoodStoragesForMediaFromCache();
        foreach ($storages_from_net as $name => $storage) {
            if (\array_key_exists($name, $storages_from_cache)) {
                foreach ($storages_from_net[$name]['files'] as $net_file) {
                    foreach ($storages_from_cache[$name]['files'] as $cache_file) {
                        if ($cache_file['name'] == $net_file['name'] && $cache_file['md5'] != $net_file['md5'] && !empty($net_file['md5']) && !empty($cache_file['md5'])) {
                            $this->addToLog('File ' . $cache_file['name'] . ' in ' . $this->media_name . ' on ' . $name . ' changed ' . $cache_file['md5'] . ' => ' . $net_file['md5']);
                        }
                    }
                }
            }
        }
    }
    private function getAllGoodStoragesForMediaFromCache()
    {
        $cache = [];
        foreach ($this->getAllCacheKeys() as $key) {
            $storage_cache = $this->db->from('storage_cache')->where(['cache_key' => $key, 'status' => 1, 'changed>' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() - $this->cache_expire_h * 3600)])->get()->all();
            if (!empty($storage_cache)) {
                $storage_cache = $storage_cache[0];
                $storage_data = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($storage_cache['storage_data']);
                if (\is_array($storage_data) && !empty($storage_data) && !empty($this->stb_storages[$storage_cache['storage_name']])) {
                    $cache[$storage_cache['storage_name']] = $storage_data;
                    $cache[$storage_cache['storage_name']]['load'] = $this->getStorageLoad($this->storages[$storage_cache['storage_name']]);
                }
            }
        }
        return $cache;
    }
    private function getAllCacheKeys()
    {
        $keys = [];
        foreach ($this->storages as $name => $storage) {
            $keys[] = $this->getCacheKey($name);
        }
        return $keys;
    }
    private function getCacheKey($storage_name)
    {
        return $storage_name . '_' . $this->media_type . '_' . $this->media_id;
    }
    private function setStorageCache($storages)
    {
        $this->db->update('storage_cache', ['status' => 0, 'changed' => '0000-00-00 00:00:00'], ['media_id' => $this->media_id, 'media_type' => $this->media_type]);
        if (!empty($storages)) {
            foreach ($storages as $name => $data) {
                $storage_data = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($data);
                $cache_key = $this->getCacheKey($name);
                $record = $this->db->from('storage_cache')->where(['cache_key' => $cache_key])->get()->first();
                if (empty($record)) {
                    $this->db->insert('storage_cache', ['cache_key' => $cache_key, 'media_type' => $this->media_type, 'media_id' => $this->media_id, 'storage_name' => $name, 'storage_data' => $storage_data, 'status' => 1, 'changed' => 'NOW()']);
                } else {
                    $this->db->update('storage_cache', ['storage_data' => $storage_data, 'status' => 1, 'changed' => 'NOW()'], ['cache_key' => $cache_key]);
                }
            }
        }
    }
    protected function sortByLoad($storages)
    {
        if (!empty($storages)) {
            foreach ($storages as $name => $storage) {
                $load[$name] = $storage['load'];
            }
            \array_multisort($load, SORT_ASC, SORT_NUMERIC, $storages);
        }
        return $storages;
    }
    protected function getMediaPath($file_name, $file_id)
    {
        return $this->media_name;
    }
    public static function createTemporaryLink($val)
    {
        $key = \md5($val . \microtime(1) . \uniqid());
        $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
        $result = $cache->set($key, $val, 0, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('vclub_tmp_link_ttl', 5));
        if ($result) {
            return $key;
        }
        return $result;
    }
    protected function incrementStorageDeny($storage_name)
    {
        $storage = $this->db->from('storage_deny')->where(['name' => $storage_name])->get()->first();
        if (empty($storage)) {
            $this->db->insert('storage_deny', ['name' => $storage_name, 'counter' => 1, 'updated' => 'NOW()']);
        } else {
            $this->db->update('storage_deny', ['counter' => $storage['counter'] + 1, 'updated' => 'NOW()'], ['name' => $storage_name]);
        }
    }
    public function createMediaDir($media_name, $extending_name = '')
    {
        if (!empty($extending_name)) {
            $media_name .= '_' . (string) $extending_name;
        }
        foreach ($this->storages as $name => $storage) {
            try {
                $this->clients[$name]->resource($this->media_type)->update(['media_name' => $media_name]);
            } catch (\Exception $exception) {
                $this->parseException($exception);
                throw new \Ministra\Lib\MasterException($exception->getMessage(), $name);
            }
        }
    }
    public function getStorageList()
    {
        return $this->storages;
    }
    public function startMD5SumInAllStorages($media_name)
    {
        foreach ($this->storages as $name => $storage) {
            try {
                $this->startMD5Sum($name, $media_name);
            } catch (\Exception $exception) {
            }
        }
    }
    public function startMD5Sum($storage_name, $media_name)
    {
        try {
            $this->clients[$storage_name]->resource($this->media_type . '_md5_checker')->create(['media_name' => $media_name]);
        } catch (\Exception $exception) {
            $this->parseException($exception);
            throw $exception;
        }
    }
    public function stopMD5Sum($storage_name, $media_name)
    {
        try {
            $this->clients[$storage_name]->resource($this->media_type . '_md5_checker')->ids($media_name)->delete();
        } catch (\Exception $exception) {
            $this->parseException($exception);
        }
    }
}
