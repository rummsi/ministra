<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class User implements \Ministra\Lib\StbApi\User
{
    private static $instance = null;
    private $id;
    private $profile;
    private $ip;
    private $verified;
    private $use_ip_ranges = null;
    private function __construct($uid = 0)
    {
        $this->id = (int) $uid;
        $this->profile = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['id' => $this->id])->get()->first();
        if ($this->profile['reseller_id'] && \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_ip_ranges', false)) {
            $this->use_ip_ranges = (int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('reseller')->where(['id' => $this->profile['reseller_id']])->get()->first('use_ip_ranges');
        }
        $this->ip = !empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : @$_SERVER['REMOTE_ADDR'];
        $country = self::getCountryCode();
        if ($country && $country != $this->profile['country']) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['country' => $country], ['id' => $this->id]);
        }
        if (!empty($this->profile)) {
            if ($this->profile['tariff_plan_id'] == 0) {
                $this->profile['tariff_plan_id'] = (int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['user_default' => 1])->get()->first('id');
            }
            $this->verified = $this->profile['verified'] === '1';
        }
    }
    public static function getCountryCode()
    {
        $ip = !empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : @$_SERVER['REMOTE_ADDR'];
        return @\geoip_country_code_by_name($ip);
    }
    public static function isInitialized()
    {
        return (bool) self::$instance;
    }
    public static function clear()
    {
        self::$instance = null;
    }
    public static function getUserAgent()
    {
        $ua = empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'];
        if (!empty($_SERVER['HTTP_X_USER_AGENT'])) {
            $ua .= '; ' . $_SERVER['HTTP_X_USER_AGENT'];
        }
        return $ua;
    }
    public static function getCountryId()
    {
        $country_code = self::getCountryCode();
        if (empty($country_code)) {
            return 0;
        }
        return (int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('countries')->where(['iso2' => $country_code])->get()->first('id');
    }
    public static function authorizeFromOss($login, $password, $mac)
    {
        $oss_wrapper = \Ministra\Lib\OssWrapper::getWrapper();
        if (!\is_callable([$oss_wrapper, 'authorize'])) {
            return false;
        }
        $info = $oss_wrapper->authorize($login, $password, $mac);
        if (!$info) {
            return false;
        }
        $key_map = ['mac' => 'stb_mac', 'ls' => 'account_number', 'fname' => 'full_name', 'tariff' => 'tariff_plan'];
        $new_account = [];
        foreach ($info as $key => $value) {
            if (\array_key_exists($key, $key_map)) {
                $new_account[$key_map[$key]] = $value;
                unset($new_account[$key]);
            } else {
                $new_account[$key] = $value;
            }
        }
        $login = empty($login) && !empty($info['login']) ? $info['login'] : $login;
        $new_account['login'] = $login;
        $new_account['password'] = $password;
        $user = \Ministra\Lib\User::getByLogin($login);
        if ($user !== false) {
            return $user;
        }
        $uid = self::createAccount($new_account);
        if (!$uid) {
            return false;
        }
        return self::getInstance($uid);
    }
    public static function getByLogin($login)
    {
        $user = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['login' => $login])->get()->first();
        if (empty($user)) {
            return false;
        }
        return self::getInstance((int) $user['id']);
    }
    public static function getInstance($uid = 0)
    {
        if (self::$instance === null || self::$instance instanceof \Ministra\Lib\User && self::$instance->getId() != -1 && !self::$instance->getProfile()) {
            self::$instance = new self($uid);
        }
        return self::$instance;
    }
    public static function createAccount($account)
    {
        $allowed_fields = ['login', 'password', 'full_name', 'phone', 'account_number', 'tariff_plan', 'tariff_plan_id', 'tariff_expired_date', 'tariff_instead_expired', 'stb_mac', 'comment', 'end_date', 'account_balance'];
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_info_for_api', false)) {
            $allowed_fields[] = 'reseller_id';
        }
        $key_map = ['full_name' => 'fname', 'account_number' => 'ls', 'stb_mac' => 'mac', 'end_date' => 'expire_billing_date'];
        $new_account = \array_intersect_key($account, \array_fill_keys($allowed_fields, true));
        if (isset($account['status'])) {
            $new_account['status'] = (int) (!$account['status']);
        }
        foreach ($new_account as $key => $value) {
            if (\array_key_exists($key, $key_map)) {
                $new_account[$key_map[$key]] = $value;
                unset($new_account[$key]);
            }
        }
        if (empty($new_account['tariff_plan_id']) && !empty($new_account['tariff_plan'])) {
            $new_account['tariff_plan_id'] = (int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['external_id' => $new_account['tariff_plan']])->get()->first('id');
        }
        if (isset($new_account['tariff_instead_expired'])) {
            $tariff = (int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['external_id' => $new_account['tariff_instead_expired']])->get()->first('id');
            $new_account['tariff_id_instead_expired'] = $tariff;
            unset($new_account['tariff_instead_expired']);
        }
        if (\array_key_exists('tariff_plan', $new_account)) {
            unset($new_account['tariff_plan']);
        }
        $new_account['created'] = 'NOW()';
        $insert_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('users', $new_account)->insert_id();
        if (!$insert_id) {
            return false;
        }
        if (!empty($new_account['password'])) {
            $password = \md5(\md5($new_account['password']) . $insert_id);
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['password' => $password], ['id' => $insert_id]);
        }
        return $insert_id;
    }
    public static function getByMac($mac)
    {
        $user = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['mac' => $mac])->get()->first();
        if (empty($user)) {
            return false;
        }
        return self::getInstance((int) $user['id']);
    }
    public static function getPackageDescription()
    {
        $package_id = (int) $_REQUEST['package_id'];
        $package = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('services_package')->where(['id' => $package_id])->get()->first();
        if (empty($package)) {
            return false;
        }
        if ($package['all_services']) {
            $service_filter = false;
        } else {
            $service_filter = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('service_in_package')->where(['package_id' => $package_id])->get()->all('service_id');
        }
        $services = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        if ($service_filter !== false) {
            $services->in('id', $service_filter);
        }
        if ($package['type'] == 'tv') {
            $services = $services->from('itv')->where(['status' => 1])->orderby('name')->get()->all('name');
        } elseif ($package['type'] == 'radio') {
            $services = $services->from('radio')->where(['status' => 1])->orderby('name')->get()->all('name');
        } elseif ($package['type'] == 'video') {
            $services = $services->from('video')->where(['status' => 1])->orderby(\sprintf(\_('video_name_format'), 'name', 'o_name'))->get()->all(\sprintf(\_('video_name_format'), 'name', 'o_name'));
        } else {
            $services = \array_unique($service_filter);
        }
        $services_str = \implode('<br>', $services);
        $type_map = ['tv' => \_('TV channels'), 'video' => \_('Movies'), 'radio' => \_('Radio channels'), 'module' => \_('Modules'), 'option' => \_('Options')];
        return ['type' => \array_key_exists($package['type'], $type_map) ? $type_map[$package['type']] : $package['type'], 'description' => \nl2br($package['description']), 'content' => $services_str];
    }
    public function getSerialNumber()
    {
        return $this->profile['serial_number'];
    }
    public function getStbType()
    {
        return $this->profile['stb_type'];
    }
    public function getLocale()
    {
        return $this->profile['locale'];
    }
    public function getLogin()
    {
        return $this->profile['login'];
    }
    public function isVerified()
    {
        return $this->verified;
    }
    public function setVerified()
    {
        $this->verified = true;
    }
    public function setSerialNumber($serial_number)
    {
        if ($this->profile['serial_number'] != $serial_number) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['serial_number' => $serial_number], ['id' => $this->id]);
        }
        return $this->profile['serial_number'] = $serial_number;
    }
    public function setMac($mac)
    {
        if ($this->profile['mac'] != $mac) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['mac' => $mac], ['id' => $this->id]);
        }
        return $this->profile['mac'] = $mac;
    }
    public function setClientType($client_type)
    {
        if ($this->profile['client_type'] != $client_type) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['client_type' => $client_type], ['id' => $this->id]);
        }
    }
    public function updateDeviceId2($device_id2)
    {
        if ($this->profile['device_id2'] != $device_id2) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['device_id2' => $device_id2], ['id' => $this->id]);
        }
    }
    public function resetAccessToken($token = '')
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['access_token' => empty($token) ? \strtoupper(\md5(\microtime(1) . \uniqid())) : $token], ['id' => $this->id])->result();
    }
    public function getExternalTariffId()
    {
        $tariff_plan_id = $this->profile['tariff_plan_id'];
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['id' => $tariff_plan_id])->get()->first('external_id');
    }
    public function getProfile()
    {
        return $this->profile;
    }
    public function refreshProfile()
    {
        $this->profile = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['id' => $this->id])->get()->first();
    }
    public function setLocale($lang)
    {
        $_COOKIE['stb_lang'] = $lang;
        if (!empty($lang) && \strlen($lang) >= 2) {
            $preferred_locales = \array_filter(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('allowed_locales'), function ($e) use($lang) {
                return \strpos($e, $lang) === 0;
            });
            if (!empty($preferred_locales)) {
                $preferred_locales = \array_values($preferred_locales);
                $locale = $preferred_locales[0];
            }
        }
        if (!isset($locale)) {
            $locales = \array_values(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('allowed_locales'));
            $locale = \array_shift($locales);
        }
        \setlocale(LC_MESSAGES, $locale);
        \putenv('LC_MESSAGES=' . $locale);
        if (!\function_exists('bindtextdomain')) {
            throw new \ErrorException('php-gettext extension not installed.');
        }
        if (!\function_exists('locale_accept_from_http')) {
            throw new \ErrorException('php-intl extension not installed.');
        }
        \bindtextdomain('stb', PROJECT_PATH . '/locale');
        \textdomain('stb');
        \bind_textdomain_codeset('stb', 'UTF-8');
    }
    public function getVideoFavorites()
    {
        $fav_video_arr = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('fav_vclub')->where(['uid' => $this->id])->get()->first();
        if (empty($fav_video_arr)) {
            return [];
        }
        $fav_video = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($fav_video_arr['fav_video']);
        if (!\is_array($fav_video)) {
            $fav_video = [];
        }
        return $fav_video;
    }
    public function getNotEndedVideo()
    {
        $not_ended_raw = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('u1.*')->from('user_played_movies u1')->join('user_played_movies u2', 'u1.video_id', 'u2.video_id AND u1.playtime<u2.playtime', 'LEFT')->where(['u1.uid' => $this->id, 'u1.file_id!=' => 0, 'u1.watched_time!=' => 0, 'u2.id' => null])->get()->all();
        if (empty($not_ended_raw)) {
            return [];
        }
        $not_ended = [];
        foreach ($not_ended_raw as $item) {
            $not_ended[$item['video_id']] = $item;
        }
        return $not_ended;
    }
    public function getWatchedVideo()
    {
        $not_ended_raw = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('user_played_movies')->where(['uid' => $this->id, 'season_id' => 0, 'episode_id' => 0, 'file_id' => 0])->get()->all();
        if (empty($not_ended_raw)) {
            return [];
        }
        $not_ended = [];
        foreach ($not_ended_raw as $item) {
            $not_ended[$item['video_id']] = $item;
        }
        return $not_ended;
    }
    public function getMovieSeasonsWatchedStatus($seasons_ids)
    {
        $watched_status_raw = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('user_played_movies')->where(['uid' => $this->id, 'episode_id' => 0, 'file_id' => 0])->in('season_id', $seasons_ids)->get()->all();
        if (empty($watched_status_raw)) {
            return [];
        }
        $watched_status = [];
        foreach ($watched_status_raw as $item) {
            $watched_status[$item['season_id']] = $item;
        }
        return $watched_status;
    }
    public function getMovieFilesWatchedStatus($movies_ids)
    {
        $watched_status_raw = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('user_played_movies')->where(['uid' => $this->id, 'file_id!=' => 0])->in('video_id', $movies_ids)->get()->all();
        if (empty($watched_status_raw)) {
            return [];
        }
        $watched_status = [];
        foreach ($watched_status_raw as $item) {
            $watched_status[$item['file_id']] = $item;
        }
        return $watched_status;
    }
    public function setNotEndedVideo($video_id, $end_time, $episode = 0)
    {
        $not_ended = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('vclub_not_ended')->where(['uid' => $this->id, 'video_id' => $video_id])->get()->first();
        if (empty($not_ended)) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('vclub_not_ended', ['uid' => $this->id, 'video_id' => $video_id, 'series' => $episode, 'end_time' => $end_time, 'added' => 'NOW()']);
        } else {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('vclub_not_ended', ['series' => $episode, 'end_time' => $end_time, 'added' => 'NOW()'], ['uid' => $this->id, 'video_id' => $video_id]);
        }
        return true;
    }
    public function setEndedVideo($video_id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('vclub_not_ended', ['uid' => $this->id, 'video_id' => $video_id])->result();
    }
    public function setTvChannelAspect($ch_id, $aspect)
    {
        $aspects = $this->getTvChannelsAspect();
        $init_required = empty($aspects);
        $aspects[(int) $ch_id] = (int) $aspect;
        $aspects = \json_encode($aspects);
        if ($init_required) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('tv_aspect', ['aspect' => $aspects, 'uid' => $this->id])->insert_id();
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('tv_aspect', ['aspect' => $aspects], ['uid' => $this->id])->result();
    }
    public function getTvChannelsAspect()
    {
        $aspect = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tv_aspect')->where(['uid' => $this->id])->get()->first('aspect');
        if (empty($aspect)) {
            return [];
        }
        $aspect = \json_decode($aspect, true);
        if (!$aspect) {
            return [];
        }
        return $aspect;
    }
    public function updateIp()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['ip' => $this->ip], ['id' => $this->id]);
    }
    public function updateKeepAlive()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['keep_alive' => 'NOW()', 'ip' => $this->ip], ['id' => $this->id]);
    }
    public function getServicesByType($type = 'tv', $service_type = null, $with_options = false)
    {
        $plan = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['id' => $this->profile['tariff_plan_id']])->get()->first();
        if (empty($plan)) {
            return;
        }
        $packages_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('package_id as id')->from('package_in_plan')->where(['plan_id' => $plan['id'], 'optional' => 0])->get()->all('id');
        $available_packages_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('package_id as id')->from('package_in_plan')->where(['plan_id' => $plan['id']])->get()->all('id');
        $subscribed_packages_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('user_package_subscription')->where(['user_id' => $this->id])->get()->all('package_id');
        $subscribed_packages_ids = \array_filter($subscribed_packages_ids, function ($package_id) use($available_packages_ids) {
            return \in_array($package_id, $available_packages_ids);
        });
        if (!empty($subscribed_packages_ids)) {
            $packages_ids = \array_merge($packages_ids, $subscribed_packages_ids);
        }
        $packages_ids = \array_unique($packages_ids);
        if (empty($packages_ids)) {
            return;
        }
        $package_where = ['type' => $type];
        if ($service_type) {
            $package_where['service_type'] = $service_type;
        }
        $packages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('services_package')->where($package_where)->in('id', $packages_ids)->get()->all();
        $contain_all_services = (bool) \array_filter($packages, function ($package) {
            return $package['all_services'] == 1;
        });
        if ($contain_all_services) {
            return 'all';
        }
        if (empty($packages)) {
            return;
        }
        $services = [];
        foreach ($packages as $package) {
            $services_raw = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('service_id, options')->from('service_in_package')->where(['package_id' => $package['id']])->orderby('service_in_package.id')->get()->all();
            foreach ($services_raw as $service) {
                $options = [];
                if ($with_options && $service['options']) {
                    $options = \json_decode($service['options'], true);
                    if (!$options) {
                        $options = [];
                    }
                }
                $services[$service['service_id']] = $options;
            }
        }
        return $with_options ? $services : \array_keys($services);
    }
    public function getTariffPlanName()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['id' => $this->profile['tariff_plan_id']])->get()->first('name');
    }
    public function getPriceForPackage($package_id)
    {
        $package = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('services_package')->where(['id' => $package_id])->get()->first();
        return \Ministra\Lib\OssWrapper::getWrapper()->getPackagePrice($package['external_id'], $package['id']);
    }
    public function getAccountInfo()
    {
        $info = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('login, fname as full_name, phone, ls as account_number, external_id as tariff_plan, ' . 'tariff_expired_date, tariff_id_instead_expired as tariff_instead_expired, ' . 'serial_number as stb_sn, mac as stb_mac, stb_type, status, ' . 'keep_alive>=FROM_UNIXTIME(UNIX_TIMESTAMP(NOW())-' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') . ') online, ip, version, comment, expire_billing_date as end_date, account_balance, ' . 'last_active');
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_info_for_api', false)) {
            $info->select(['reseller.id as reseller_id', 'reseller.name as reseller_name'])->join('reseller', 'users.reseller_id', 'reseller.id', 'LEFT');
        }
        $info = $info->from('users')->join('tariff_plan', 'tariff_plan_id', 'tariff_plan.id', 'LEFT')->where(['users.id' => $this->id])->get()->first();
        $info['status'] = (int) (!$info['status']);
        if ($info['tariff_plan'] === null) {
            $info['tariff_plan'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['user_default' => 1])->get()->first('external_id');
        }
        if ($info['tariff_expired_date']) {
            if ($info['tariff_instead_expired'] == 0) {
                $info['tariff_instead_expired'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['user_default' => 1])->get()->first('external_id');
            } else {
                $info['tariff_instead_expired'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['id' => $info['tariff_instead_expired']])->get()->first('external_id');
            }
        }
        if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_internal_billing', false)) {
            unset($info['end_date']);
        }
        $packages = $this->getPackages();
        $info['subscribed'] = [];
        $info['subscribed_id'] = [];
        if (\count($packages) > 0) {
            $subscribed_packages = \array_filter($packages, function ($package) {
                return $package['optional'] == 1 && $package['subscribed'];
            });
            foreach ($subscribed_packages as $package) {
                $info['subscribed'][] = $package['external_id'];
                $info['subscribed_id'][] = $package['package_id'];
            }
        }
        return $info;
    }
    public function getPackages()
    {
        $plan = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['id' => $this->profile['tariff_plan_id']])->get()->first();
        if (empty($plan)) {
            return;
        }
        $packages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('package_in_plan.*, services_package.id as services_package_id, ' . 'services_package.name as name, services_package.type as type, ' . 'services_package.external_id as external_id, ' . 'services_package.description as description, ' . 'services_package.service_type as service_type')->from('package_in_plan')->join('services_package', 'services_package.id', 'package_in_plan.package_id', 'INNER')->where(['plan_id' => $plan['id']])->orderby('package_in_plan.optional, external_id')->get()->all();
        $subscribed_packages_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('user_package_subscription')->where(['user_id' => $this->id])->get()->all('package_id');
        $packages = \array_map(function ($package) use($subscribed_packages_ids) {
            if ($package['optional'] == 1) {
                $package['subscribed'] = \in_array($package['package_id'], $subscribed_packages_ids);
            } else {
                $package['subscribed'] = true;
            }
            return $package;
        }, $packages);
        return $packages;
    }
    public function updateAccount($account)
    {
        $allowed_fields = ['login', 'password', 'full_name', 'phone', 'account_number', 'tariff_plan', 'tariff_expired_date', 'tariff_instead_expired', 'stb_mac', 'comment', 'end_date', 'account_balance'];
        $key_map = ['full_name' => 'fname', 'account_number' => 'ls', 'stb_mac' => 'mac', 'end_date' => 'expire_billing_date'];
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_info_for_api', false)) {
            $allowed_fields[] = 'reseller_id';
        }
        $new_account = \array_intersect_key($account, \array_fill_keys($allowed_fields, true));
        if (isset($account['status'])) {
            $this->setStatus($account['status']);
            if (empty($new_account)) {
                return true;
            }
        }
        foreach ($new_account as $key => $value) {
            if (\array_key_exists($key, $key_map)) {
                $new_account[$key_map[$key]] = $value;
                unset($new_account[$key]);
            }
        }
        if (!empty($new_account['tariff_plan'])) {
            $new_account['tariff_plan_id'] = (int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['external_id' => $new_account['tariff_plan']])->get()->first('id');
            unset($new_account['tariff_plan']);
        }
        if (isset($new_account['tariff_instead_expired'])) {
            $tariff = (int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['external_id' => $new_account['tariff_instead_expired']])->get()->first('id');
            $new_account['tariff_id_instead_expired'] = $tariff;
            unset($new_account['tariff_instead_expired']);
        }
        if (!empty($new_account['password'])) {
            $password = \md5(\md5($new_account['password']) . $this->id);
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['password' => $password], ['id' => $this->id]);
            unset($new_account['password']);
        } else {
            unset($new_account['password']);
        }
        if (!empty($new_account['mac'])) {
            $new_account['access_token'] = '';
            $new_account['device_id'] = '';
            $new_account['device_id2'] = '';
        }
        if (empty($new_account)) {
            return true;
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', $new_account, ['id' => $this->id])->result();
    }
    public function setStatus($status)
    {
        $status = (int) (!$status);
        if ($status == $this->profile['status']) {
            return;
        }
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['status' => $status], ['id' => $this->id]);
        $event = new \Ministra\Lib\SysEvent();
        $event->setUserListById($this->id);
        if ($status == 1) {
            $event->sendCutOff();
        } else {
            $event->sendCutOn();
        }
    }
    public function delete()
    {
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('fav_itv', ['uid' => $this->id]);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('fav_vclub', ['uid' => $this->id]);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('media_favorites', ['uid' => $this->id]);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('access_tokens', ['uid' => $this->id]);
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('users', ['id' => $this->id])->result();
    }
    public function updateOptionalPackageSubscription($params)
    {
        if (empty($params['subscribe']) && empty($params['subscribe_ids']) && empty($params['unsubscribe']) && empty($params['unsubscribe_ids'])) {
            return false;
        }
        $packages = $this->getPackages();
        $total_result = false;
        if (!empty($params['subscribe'])) {
            if (!\is_array($params['subscribe'])) {
                $params['subscribe'] = [$params['subscribe']];
            }
            $user_packages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('services_package')->in('external_id', $params['subscribe'])->get()->all();
            foreach ($user_packages as $user_package) {
                $result = $this->subscribeToPackage($user_package['id'], $packages, true);
                $total_result = $total_result || $result;
            }
        }
        if (!empty($params['subscribe_ids']) && \is_array($params['subscribe_ids'])) {
            foreach ($params['subscribe_ids'] as $package_id) {
                $result = $this->subscribeToPackage($package_id, $packages, true);
                $total_result = $total_result || $result;
            }
        }
        if (!empty($params['unsubscribe'])) {
            if (!\is_array($params['unsubscribe'])) {
                $params['unsubscribe'] = [$params['unsubscribe']];
            }
            $user_packages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('services_package')->in('external_id', $params['unsubscribe'])->get()->all();
            foreach ($user_packages as $user_package) {
                $result = $this->unsubscribeFromPackage($user_package['id'], $packages, true);
                $total_result = $total_result || $result;
            }
        }
        if (!empty($params['unsubscribe_ids']) && \is_array($params['unsubscribe_ids'])) {
            foreach ($params['unsubscribe_ids'] as $package_id) {
                $result = $this->unsubscribeFromPackage($package_id, $packages, true);
                $total_result = $total_result || $result;
            }
        }
        return $total_result;
    }
    public function subscribeToPackage($package_id, $packages = null, $force_no_check_billing = false)
    {
        if ($packages === null) {
            $packages = $this->getPackages();
        }
        $filtered_packages = null;
        if ($packages != null) {
            $filtered_packages = \array_filter($packages, function ($item) use($package_id) {
                return $package_id == $item['package_id'] && ($item['optional'] == 1 && !$item['subscribed'] || $item['service_type'] == 'single');
            });
        }
        if (empty($filtered_packages)) {
            return false;
        }
        if (!$force_no_check_billing) {
            $ext_package_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('services_package')->where(['id' => $package_id])->get()->first('external_id');
            $on_subscribe_result = \Ministra\Lib\OssWrapper::getWrapper()->subscribeToPackage($ext_package_id);
            if ($on_subscribe_result === true) {
                \Ministra\Lib\Log::writePackageSubscribeLog($this->id, $package_id, 1);
                return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('user_package_subscription', ['user_id' => $this->id, 'package_id' => $package_id])->insert_id();
            }
            return false;
        }
        $return = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('user_package_subscription', ['user_id' => $this->id, 'package_id' => $package_id])->insert_id();
        \Ministra\Lib\Log::writePackageSubscribeLog($this->id, $package_id, 1);
        $event = new \Ministra\Lib\SysEvent();
        $event->setUserListById($this->id);
        $event->setTtl(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2);
        $event->sendMsgAndReboot($this->getLocalizedText('Services are updated according to the subscription. The STB will be rebooted.'));
        return $return;
    }
    public function getLocalizedText($text)
    {
        $current_local = \setlocale(LC_MESSAGES, 0);
        $user_locale = $this->getProfileParam('locale');
        if ($user_locale) {
            \setlocale(LC_MESSAGES, $user_locale);
            \putenv('LC_MESSAGES=' . $user_locale);
            $text = \_($text);
            \setlocale(LC_MESSAGES, $current_local);
            \putenv('LC_MESSAGES=' . $current_local);
        }
        return $text;
    }
    public function getProfileParam($param)
    {
        return $this->profile[$param];
    }
    public function unsubscribeFromPackage($package_id, $packages = null, $force_no_check_billing = false)
    {
        if ($packages === null) {
            $packages = $this->getPackages();
        }
        $filtered_packages = \array_filter($packages, function ($item) use($package_id) {
            return $package_id == $item['package_id'] && $item['optional'] == 1 && $item['subscribed'];
        });
        if (empty($filtered_packages)) {
            return false;
        }
        if (!$force_no_check_billing) {
            $ext_package_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('services_package')->where(['id' => $package_id])->get()->first('external_id');
            $on_unsubscribe_result = \Ministra\Lib\OssWrapper::getWrapper()->unsubscribeFromPackage($ext_package_id);
            \var_dump($on_unsubscribe_result);
            if ($on_unsubscribe_result === true) {
                \Ministra\Lib\Log::writePackageSubscribeLog($this->id, $package_id, 0);
                return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('user_package_subscription', ['user_id' => $this->id, 'package_id' => $package_id])->result();
            }
            return false;
        }
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('user_package_subscription', ['user_id' => $this->id, 'package_id' => $package_id])->result();
        \Ministra\Lib\Log::writePackageSubscribeLog($this->id, $package_id, 0);
        $event = new \Ministra\Lib\SysEvent();
        $event->setUserListById($this->id);
        $event->setTtl(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2);
        $event->sendMsgAndReboot($this->getLocalizedText('Services are updated according to the subscription. The STB will be rebooted.'));
        return $result;
    }
    public function updateUserInfoFromOSS()
    {
        $info = $this->getInfoFromOSS();
        if (!$info) {
            return false;
        }
        $update_data = [];
        if (\array_key_exists('ls', $info)) {
            $this->profile['ls'] = $update_data['ls'] = $info['ls'];
        }
        if (\array_key_exists('status', $info)) {
            $this->profile['status'] = $update_data['status'] = (int) (!$info['status']);
        }
        if (\array_key_exists('additional_services_on', $info)) {
            $this->profile['additional_services_on'] = $update_data['additional_services_on'] = (int) $info['additional_services_on'];
        }
        if (\array_key_exists('fname', $info)) {
            $this->profile['fname'] = $update_data['fname'] = $info['fname'];
        }
        if (\array_key_exists('phone', $info)) {
            $this->profile['phone'] = $update_data['phone'] = $info['phone'];
        }
        if (\array_key_exists('tariff', $info)) {
            $tariff = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['external_id' => $info['tariff']])->get()->first();
            if ($tariff) {
                $tariff_id = $tariff['id'];
            } else {
                $tariff_id = 0;
            }
            $this->profile['tariff_plan_id'] = $update_data['tariff_plan_id'] = $tariff_id;
        }
        if (empty($update_data)) {
            return false;
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', $update_data, ['id' => $this->id]);
    }
    public function getInfoFromOSS()
    {
        try {
            return \Ministra\Lib\OssWrapper::getWrapper()->getUserInfo($this);
        } catch (\Ministra\Lib\OssException $e) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::a33a714359e5728f5f22fe703d8999b7($e);
            return ['status' => 0];
        }
    }
    public function getLastChannelId()
    {
        return (int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('last_id')->where(['uid' => $this->id])->get()->first('last_id');
    }
    public function setLastChannelId($ch_id)
    {
        $last_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('last_id')->where(['uid' => $this->id])->get()->first();
        if (empty($last_id)) {
            return (bool) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('last_id', ['ident' => $this->getMac(), 'last_id' => $ch_id, 'uid' => $this->id])->insert_id();
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('last_id', ['last_id' => $ch_id], ['uid' => $this->id])->result();
    }
    public function getMac()
    {
        return empty($this->profile['mac']) ? null : $this->profile['mac'];
    }
    public function rentVideo($video_id, $price = 0)
    {
        $rented = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_rent')->where(['video_id' => $video_id, 'uid' => $this->id])->get()->first();
        $package = $this->getPackageByVideoId($video_id);
        if (empty($package)) {
            return false;
        }
        $rent_data = ['uid' => $this->id, 'video_id' => $video_id, 'price' => $price, 'rent_date' => 'NOW()', 'rent_end_date' => \date('Y-m-d H:i:s', \time() + $package['rent_duration'] * 3600)];
        $rent_history_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('video_rent_history', $rent_data)->insert_id();
        $rent_data['rent_history_id'] = $rent_history_id;
        if (empty($rented)) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('video_rent', $rent_data)->insert_id();
        }
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('video_rent', $rent_data, ['id' => $rented['id']])->result();
        if (!$result) {
            return false;
        }
        return (int) $rented['id'];
    }
    public function getPackageByVideoId($video_id)
    {
        return $this->getPackageByServiceId($video_id, 'video');
    }
    public function getPackageByServiceId($service_id, $type)
    {
        $user_packages = $this->getPackages();
        if (empty($user_packages)) {
            return;
        }
        $user_packages = \array_filter($user_packages, function ($package) {
            return $package['subscribed'];
        });
        if (empty($user_packages)) {
            return;
        }
        $user_packages_ids = \array_map(function ($package) {
            return $package['package_id'];
        }, $user_packages);
        $user_packages_ids = \array_values($user_packages_ids);
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('services_package.*')->from('services_package')->where(['service_id' => $service_id, 'services_package.type' => $type])->join('service_in_package', 'services_package.id', 'package_id', 'INNER')->in('services_package.id', $user_packages_ids)->get()->first();
    }
    public function getAllRentedVideo()
    {
        $raw = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('select * from video_rent where uid=' . $this->id . ' and (rent_end_date>NOW() OR rent_date=rent_end_date)')->all();
        $map = [];
        foreach ($raw as $rent) {
            if ($rent['rent_date'] != $rent['rent_end_date']) {
                $rent['expires_in'] = self::humanDateDiff($rent['rent_end_date']);
            }
            $map[$rent['video_id']] = $rent;
        }
        return $map;
    }
    public static function humanDateDiff($date1, $date2 = 'now')
    {
        $diff_str = '';
        $ts1 = \strtotime($date1);
        $ts2 = \strtotime($date2);
        if (!$ts1 || !$ts1) {
            return false;
        }
        $diff_seconds = $ts1 - $ts2;
        $days = \floor($diff_seconds / 86400);
        $hours = \floor(($diff_seconds - $days * 86400) / 3600);
        $minutes = \floor(($diff_seconds - ($days * 86400 + $hours * 3600)) / 60);
        if ($days) {
            $diff_str .= \sprintf(\ngettext('%d day', '%d days', $days), $days) . ' ';
        }
        if ($hours) {
            $diff_str .= $hours . \_('h') . ' ';
        }
        if ($minutes) {
            $diff_str .= $minutes . \_('min') . ' ';
        }
        return $diff_str;
    }
    public function userCheckIPTimeout()
    {
        if (!empty($this->use_ip_ranges)) {
            if (!$this->checkIpInResellerRanges()) {
                if (empty($this->profile['last_change_ip'])) {
                    $this->profile['last_change_ip'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['id' => $this->id])->get()->first('last_change_ip');
                } elseif (\is_string($this->profile['last_change_ip'])) {
                    $this->profile['last_change_ip'] = \json_decode($this->profile['last_change_ip']);
                    if (!$this->profile['last_change_ip'] || empty($this->profile['last_change_ip'])) {
                        $this->profile['last_change_ip'] = [0 => ''];
                    }
                }
                \reset($this->profile['last_change_ip']);
                list($timestamp, $ip) = \each($this->profile['last_change_ip']);
                $config_time_out = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('user_wrong_ip_timeout', 0);
                if ($config_time_out) {
                    return \time() - $timestamp <= $config_time_out;
                }
                return false;
            } elseif (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_ip_ranges', false)) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['last_change_ip' => \json_encode([\time() => $this->ip])], ['id' => $this->id]);
            }
        }
        return true;
    }
    public function checkIpInResellerRanges($ip = '')
    {
        if (!empty($this->id) && \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('allow_resellers_ip_ranges', false)) {
            $ip = empty($ip) ? (string) $this->ip : $ip;
            if (!empty($this->profile['reseller_id']) && !empty($ip)) {
                if (empty($this->profile['resellers_ips_ranges'])) {
                    $ranges = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('resellers_ips_ranges')->where(['reseller_id' => (int) $this->profile['reseller_id']])->get()->all();
                    $this->profile['resellers_ips_ranges'] = $ranges;
                }
                $ip_to_long = \ip2long($ip);
                foreach ($this->profile['resellers_ips_ranges'] as $resellers_ips_range) {
                    if ($resellers_ips_range['calculated_range_begin'] <= $ip_to_long && $ip_to_long <= $resellers_ips_range['calculated_range_end']) {
                        return true;
                    }
                }
                return false;
            }
        }
        return true;
    }
    public function userErrorLog($msg = '')
    {
        $logger = new \Ministra\Lib\Logger();
        $logger->setPrefix('user_' . $this->getId() . '_');
        $date = new \DateTime('now', new \DateTimeZone(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('default_timezone')));
        $logger->error(\sprintf("[%s] %s\nMessage:%s\nIP-address:%s\n-------\n", $date->format('r'), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->mac, $msg, $this->getIp()));
    }
    public function getId()
    {
        return $this->id;
    }
    public function getIp()
    {
        return $this->ip;
    }
}
