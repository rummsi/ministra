<?php

namespace Ministra\Lib;

use Exception;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Y5e6e40f383d7e1d4e0a0b052a8423153;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class Itv extends \Ministra\Lib\AjaxResponse implements \Ministra\Lib\StbApi\Itv
{
    public static $instance = null;
    private static $channels_cache = array();
    private static $links_cache = array();
    private $all_user_channels_ids;
    private $user_channel_options = array();
    private $dvb_channels = null;
    private $include_censored = true;
    private $fav_itv = null;
    private $censored_channels = null;
    public function __construct()
    {
        parent::__construct();
    }
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public static function checkTemporaryLink($key)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance()->get($key);
    }
    public static function getChannelNameById($id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['id' => $id])->use_caching(['itv.id=' . (int) $id])->get()->first('name');
    }
    public static function getChannelById($id)
    {
        return self::getById($id);
    }
    public static function invalidateCacheForChannel($id)
    {
        if (isset(self::$channels_cache[(string) $id])) {
            unset(self::$channels_cache[(string) $id]);
        }
    }
    public static function getLogoUriById($id, $resolution = 320)
    {
        $channel = \Ministra\Lib\Itv::getById($id);
        if (empty($channel['logo'])) {
            return '';
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('portal_url') . 'misc/logos/' . $resolution . '/' . $channel['logo'];
    }
    public static function delLogoById($id)
    {
        $paths = self::getLogoPathsById($id);
        foreach ($paths as $path) {
            if ($path) {
                \unlink($path);
            }
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('itv', ['logo' => ''], ['id' => $id])->result();
    }
    public static function getLogoPathsById($id)
    {
        $channel = \Ministra\Lib\Itv::getById($id);
        if (empty($channel['logo'])) {
            return;
        }
        return [\realpath(PROJECT_PATH . '/../misc/logos/120/' . $channel['logo']), \realpath(PROJECT_PATH . '/../misc/logos/160/' . $channel['logo']), \realpath(PROJECT_PATH . '/../misc/logos/240/' . $channel['logo']), \realpath(PROJECT_PATH . '/../misc/logos/320/' . $channel['logo'])];
    }
    public static function getServices()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('id, CONCAT_WS(". ", cast(number as char), name) as name')->from('itv')->orderby('number')->get()->all();
    }
    public static function setChannelLinkStatus($link_id, $status)
    {
        if (\strpos($link_id, 's') === 0) {
            $balanser_link_id = \substr($link_id, 1);
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('ch_link_on_streamer', ['monitoring_status' => $status], ['id' => $balanser_link_id]);
            $balanser_link = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_link_on_streamer')->where(['id' => $balanser_link_id])->get()->first();
            if (empty($balanser_link)) {
                return false;
            }
            $link = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_links')->where(['id' => $balanser_link['link_id']])->get()->first();
            if (empty($link)) {
                return false;
            }
            if ($status == 0) {
                $other_good_balanser_links = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_link_on_streamer')->where(['link_id' => $link['id'], 'id!=' => $balanser_link_id, 'monitoring_status' => 1])->get()->all();
                if (empty($other_good_balanser_links)) {
                    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('ch_links', ['status' => $status], ['id' => $link['id']]);
                }
            } else {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('ch_links', ['status' => $status], ['id' => $link['id']]);
            }
            $ch_id = $link['ch_id'];
        } else {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('ch_links', ['status' => $status], ['id' => $link_id]);
            $ch_id = (int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_links')->where(['id' => $link_id])->get()->first('ch_id');
        }
        $channel = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['id' => $ch_id])->get()->first();
        if (empty($channel)) {
            return false;
        }
        $good_links = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_links')->where(['ch_id' => $ch_id, 'status' => 1])->get()->all();
        if (!empty($good_links) && $channel['monitoring_status'] == 0) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('itv', ['monitoring_status' => 1], ['id' => $ch_id]);
            if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('administrator_email')) {
                $message = \sprintf(\_('Channel %s set to active because at least one of its URLs became available.'), $channel['number'] . ' ' . $channel['name']);
                \mail(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('administrator_email'), 'Channels monitoring report: channel enabled', $message, "Content-type: text/html; charset=UTF-8\r\n");
            }
        } else {
            if (empty($good_links) && $channel['monitoring_status'] == 1) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('itv', ['monitoring_status' => 0], ['id' => $ch_id]);
                if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('administrator_email')) {
                    $message = \sprintf(\_('Channel %s set to inactive because all its URLs are not available.'), $channel['number'] . ' ' . $channel['name']);
                    \mail(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('administrator_email'), 'Channels monitoring report: channel disabled', $message, "Content-type: text/html; charset=UTF-8\r\n");
                }
            }
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('itv', ['monitoring_status_updated' => 'NOW()'], ['id' => $ch_id])->result();
    }
    public function createLink()
    {
        $disable_ad = $_REQUEST['disable_ad'];
        $force_ch_link_check = isset($_REQUEST['force_ch_link_check']) ? $_REQUEST['force_ch_link_check'] : false;
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans')) {
            $user = \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
            $options = $user->getServicesByType('option');
            if ($options && \array_search('disable_ad', $options) !== false) {
                $disable_ad = true;
            }
        }
        if (!$disable_ad) {
            $advertising = new \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Y5e6e40f383d7e1d4e0a0b052a8423153();
            $campaigns = $advertising->d5a029fa3f44597a7bd0107b4e74bbdb(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id, 104);
        }
        $playlist = [];
        if (isset($campaigns) && \is_array($campaigns) && \count($campaigns) > 0) {
            foreach ($campaigns as $campaign) {
                if (!empty($campaign['campaign']['places'][104])) {
                    $playlist[] = ['id' => $campaign['campaign']['id'], 'media_type' => 'advert', 'cmd' => 'ffmpeg ' . $campaign['ad'], 'is_advert' => true, 'ad_tracking' => $campaign['tracking'], 'ad_must_watch' => $campaign['skip_after'] == 0 ? 'all' : $campaign['skip_after'] . 's'];
                }
            }
        }
        if (\count($playlist)) {
            return $playlist;
        }
        $cmd = '';
        $streamer_id = 0;
        $link_id = 0;
        $load = 0;
        \preg_match("/\\/ch\\/(\\d+)(.*)/", $_REQUEST['cmd'], $tmp_arr);
        if (empty($tmp_arr)) {
            $error = 'nothing_to_play';
        }
        $extra = $tmp_arr[2];
        $link_id = (int) $tmp_arr[1];
        $link = \Ministra\Lib\Itv::getLinkById($link_id);
        $channel = \Ministra\Lib\Itv::getById($link['ch_id']);
        $ch_id = $channel['id'];
        try {
            if (!empty($link)) {
                $link_id = $link['id'];
                if ($link['status'] == 0 || \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('force_ch_link_check', false) || $force_ch_link_check || !empty($_REQUEST['for_pvr']) && \strpos($link['url'], '.m3u8')) {
                    $alternative_links = self::getUrlsForChannel($ch_id, !empty($_REQUEST['for_pvr']), $force_ch_link_check);
                    if (empty($alternative_links)) {
                        throw new \Ministra\Lib\ItvLinkException('nothing_to_play');
                    }
                    $link = $alternative_links[0];
                    $link_id = $link['id'];
                }
            } else {
                $link_id = null;
            }
            $real_channel = $this->getRealChannelByChannelId($ch_id, $link_id);
            $cmd = $real_channel['cmd'];
            $streamer_id = empty($real_channel['streamer_id']) ? 0 : (int) $real_channel['streamer_id'];
            $link_id = empty($real_channel['link_id']) ? 0 : (int) $real_channel['link_id'];
            $load = empty($real_channel['load']) ? 0 : (int) $real_channel['load'];
        } catch (\Ministra\Lib\ItvLinkException $e) {
            $error = $e->getMessage();
            echo $e->getTraceAsString();
        } catch (\Exception $e) {
            $error = 'link_fault';
            echo $e->getTraceAsString();
        }
        $res = ['id' => $ch_id, 'cmd' => empty($error) ? $cmd . $extra : '', 'streamer_id' => $streamer_id, 'link_id' => $link_id, 'load' => $load, 'error' => empty($error) ? '' : $error];
        \var_dump($res);
        return $res;
    }
    public static function getLinkById($id)
    {
        if (isset(self::$links_cache[(string) $id])) {
            return self::$links_cache[(string) $id];
        }
        $link = self::$links_cache[(string) $id] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_links')->where(['id' => $id])->use_caching(['ch_links.id=' . (int) $id])->get()->first();
        return $link;
    }
    public static function getById($id)
    {
        if (isset(self::$channels_cache[(string) $id])) {
            return self::$channels_cache[(string) $id];
        }
        $channel = self::$channels_cache[(string) $id] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['id' => (int) $id])->use_caching(['itv.id=' . (int) $id])->get()->first();
        return $channel;
    }
    public static function getUrlsForChannel($ch_id, $for_pvr = false, $force_ch_link_check = false)
    {
        $user_channel_links = [];
        $channel_links = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_links')->where(['ch_id' => $ch_id, 'status' => 1])->orderby('priority, rand()')->get()->all();
        if (!self::isDisabledChannelsByMac() && !self::isDisabledChannelsBylogin()) {
            $user_agent = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->e201dd29ac754599b321778554c12cc6();
            $user_channel_links = self::filterLinksByUserAgentFilter($channel_links, $user_agent);
        }
        if (empty($user_channel_links)) {
            $user_channel_links = \array_filter($channel_links, function ($link) {
                return $link['user_agent_filter'] == '';
            });
        }
        if ($for_pvr) {
            $user_channel_links = \array_filter($user_channel_links, function ($link) {
                return \strpos($link['url'], '.m3u8') === false;
            });
        }
        $user_channel_links = \array_map(function ($link) use($force_ch_link_check) {
            if ($link['use_http_tmp_link'] == 1 || $link['use_load_balancing'] == 1 || \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('force_ch_link_check', false) || $force_ch_link_check) {
                if (\preg_match("/(\\w+)\\s+http:/", $link['url'], $match)) {
                    $solution = $match[1];
                } else {
                    $solution = 'ffrt';
                }
                $link['url'] = $solution . ' http://' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('stream_proxy') . '/ch/' . $link['id'];
            }
            unset($link['monitoring_url']);
            return $link;
        }, $user_channel_links);
        return \array_values($user_channel_links);
    }
    private static function isDisabledChannelsByMac()
    {
        $disable_channel_filter_for_macs = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('disable_channel_filter_for_macs', []);
        if (\is_string($disable_channel_filter_for_macs)) {
            $disable_channel_filter_for_macs = empty($disable_channel_filter_for_macs) ? [] : [$disable_channel_filter_for_macs];
        }
        $mac = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->mac;
        return \in_array($mac, $disable_channel_filter_for_macs);
    }
    private static function isDisabledChannelsBylogin()
    {
        $disable_channel_filter_for_logins = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('disable_channel_filter_for_logins', []);
        $login = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('login');
        return \in_array($login, $disable_channel_filter_for_logins);
    }
    public static function filterLinksByUserAgentFilter(array $allLinks, $userAgent = null)
    {
        if (\is_null($userAgent)) {
            $userAgent = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->e201dd29ac754599b321778554c12cc6();
        }
        return \array_filter($allLinks, function ($link) use($userAgent) {
            try {
                return $link['user_agent_filter'] == '' || \preg_match('/' . $link['user_agent_filter'] . '/', $userAgent) && !\preg_last_error() || \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::o5fb04dce2e0b53a5a63d129898489093($link['user_agent_filter']);
            } catch (\Exception $e) {
                return false;
            }
        });
    }
    public function getRealChannelByChannelId($ch_id, $link_id = null)
    {
        $ch_id = (int) $ch_id;
        $channel = \Ministra\Lib\Itv::getById($ch_id);
        $channel['link_id'] = $link_id;
        if ($link_id != null) {
            $link = \Ministra\Lib\Itv::getLinkById($link_id);
            if (!empty($link)) {
                $channel['cmd'] = $link['url'];
                $channel['use_http_tmp_link'] = $link['use_http_tmp_link'];
                $channel['wowza_tmp_link'] = $link['wowza_tmp_link'];
                $channel['nginx_secure_link'] = $link['nginx_secure_link'];
                $channel['use_load_balancing'] = $link['use_load_balancing'];
                $channel['xtream_codes_support'] = $link['xtream_codes_support'];
                $channel['nimble_auth_support'] = $link['nimble_auth_support'];
                $channel['edgecast_auth_support'] = $link['edgecast_auth_support'];
                $channel['akamai_auth_support'] = $link['akamai_auth_support'];
                $channel['flexcdn_auth_support'] = $link['flexcdn_auth_support'];
            }
        }
        if (empty($link_id) || empty($link)) {
            throw new \Ministra\Lib\ItvChannelTemporaryUnavailable();
        }
        if (empty($channel)) {
            throw new \Ministra\Lib\ItvLinkException('nothing_to_play');
        }
        if ($channel['enable_wowza_load_balancing']) {
            $balancer_addr = $this->getWowzaBalancer($channel['cmd']);
            $edge = $this->getWowzaEdge('http://' . $balancer_addr . '/loadbalancer');
            if (!$edge) {
                throw new \Ministra\Lib\ItvLinkException('nothing_to_play');
            }
            $cmd = \preg_replace('/' . \preg_replace('/:.*/', '', $balancer_addr) . '/', $edge, $channel['cmd']);
            if ($cmd) {
                $channel['cmd'] = $cmd;
            }
        }
        if ($channel['use_load_balancing']) {
            try {
                $streamers = \Ministra\Lib\StreamServer::getForLink($link_id);
            } catch (\Exception $e) {
                throw new \Ministra\Lib\ItvLinkException($e->getCode());
            }
            if ($streamers) {
                $new_addr = $streamers[0]['address'];
                $channel['load'] = $streamers[0]['load'];
                if ($channel['load'] >= 1) {
                    throw new \Ministra\Lib\ItvLinkException('limit');
                }
                $channel['streamer_id'] = $streamers[0]['id'];
                $channel['cmd'] = \preg_replace('/:\\/\\/([^\\/]*)/', '://' . $new_addr, $channel['cmd']);
            } else {
                throw new \Ministra\Lib\ItvLinkException('nothing_to_play');
            }
        }
        if ($channel['use_http_tmp_link']) {
            if ($channel['wowza_tmp_link']) {
                $key = $this->createTemporaryLink('1');
                if (!$key) {
                    throw new \Ministra\Lib\ItvLinkException('link_fault');
                }
                if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('use_named_wowza_token', false)) {
                    $channel['cmd'] = $channel['cmd'] . (\strpos($channel['cmd'], '?') ? '&' : '?') . 'token=' . $key;
                } else {
                    $channel['cmd'] = $channel['cmd'] . '?' . $key;
                }
            } elseif ($channel['xtream_codes_support']) {
                $max_seconds = 3;
                if (\preg_match('/\\/([0-9]+)\\.ts$/', $channel['cmd'], $id_match) && (\extension_loaded('mcrypt') || \extension_loaded('mcrypt.so'))) {
                    $key = \Ministra\Lib\XtreamCodes::getHash($this->stb->mac, $this->stb->ip, $id_match[1], $max_seconds);
                    if (!$key) {
                        throw new \Ministra\Lib\ItvLinkException('link_fault');
                    }
                    $channel['cmd'] .= (\strpos($channel['cmd'], '?') ? '&' : '?') . 'stalker_key=' . $key;
                } else {
                    throw new \Ministra\Lib\ItvLinkException('link_fault');
                }
            } elseif ($channel['edgecast_auth_support']) {
                $token = self::getEdgeCastAuthToken();
                if (!$token && (\extension_loaded('mcrypt') || \extension_loaded('mcrypt.so'))) {
                    throw new \Ministra\Lib\ItvLinkException('link_fault');
                }
                $channel['cmd'] = $channel['cmd'] . (\strpos($channel['cmd'], '?') ? '&' : '?') . $token;
            } elseif ($channel['akamai_auth_support']) {
                $token = self::getAkamaiToken();
                if (!$token) {
                    throw new \Ministra\Lib\ItvLinkException('link_fault');
                }
                $channel['cmd'] = $channel['cmd'] . (\strpos($channel['cmd'], '?') ? '&' : '?') . $token;
            } else {
                if (!empty($link) && $link['flussonic_tmp_link']) {
                    $key = $this->createTemporaryLink($this->stb->id);
                    if (!$key) {
                        throw new \Ministra\Lib\ItvLinkException('link_fault');
                    }
                    $channel['cmd'] = $channel['cmd'] . (\strpos($channel['cmd'], '?') ? '&' : '?') . 'token=' . $key;
                } else {
                    if ($channel['nginx_secure_link']) {
                        $channel['cmd'] = self::getNginxSecureLink($channel['cmd']);
                    } else {
                        if ($channel['nimble_auth_support']) {
                            $channel['cmd'] .= \strpos($channel['cmd'], '?') !== false ? '&' : '?';
                            $channel['cmd'] .= \preg_match('/https?\\:\\/\\//i', $channel['cmd']) ? self::getNimbleHttpAuthToken($channel['cmd']) : self::getNimbleRtspAuthToken($channel['cmd']);
                        } else {
                            if (!empty($link) && $link['wowza_securetoken']) {
                                $token = self::getWowzaSecureToken($channel['cmd']);
                                if (!$token) {
                                    throw new \Ministra\Lib\ItvLinkException('link_fault');
                                }
                                $channel['cmd'] = $channel['cmd'] . (\strpos($channel['cmd'], '?') ? '&' : '?') . $token;
                            } else {
                                if ($channel['flexcdn_auth_support']) {
                                    $url = $this->getFlexCDNToken($channel['cmd']);
                                    if (!$url) {
                                        throw new \Ministra\Lib\ItvLinkException('link_fault');
                                    }
                                    $channel['cmd'] = $url;
                                } else {
                                    if (\strpos($channel['cmd'], 'rtp://') !== false || \strpos($channel['cmd'], 'udp://') !== false) {
                                        return $channel;
                                    }
                                    if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('stream_proxy', '') != '') {
                                        \preg_match("/http:\\/\\/([^\\/]*)[\\/]?([^\\s]*)?(\\s*)?(.*)?\$/", $channel['cmd'], $tmp_url_arr);
                                    } else {
                                        \preg_match("/http:\\/\\/([^\\/]*)\\/([^\\/]*)[\\/]?([^\\s]*)?(\\s*)?(.*)?\$/", $channel['cmd'], $tmp_url_arr);
                                    }
                                    if (empty($tmp_url_arr)) {
                                        throw new \Ministra\Lib\ItvLinkException('nothing_to_play');
                                    }
                                    if (\count($tmp_url_arr) == 6) {
                                        $streamer = $tmp_url_arr[1];
                                        $redirect_host = $tmp_url_arr[2];
                                        $redirect_uri = $tmp_url_arr[3];
                                    } else {
                                        $streamer = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('stream_proxy');
                                        $redirect_host = $tmp_url_arr[1];
                                        $redirect_uri = $tmp_url_arr[2];
                                    }
                                    $redirect_url = '/get/' . $redirect_host . '/' . $redirect_uri;
                                    $link_result = $this->createTemporaryLink($redirect_url);
                                    if (!$link_result) {
                                        throw new \Ministra\Lib\ItvLinkException('link_fault');
                                    }
                                    if (\preg_match("/(\\w+)\\s+http:/", $channel['cmd'], $match)) {
                                        $solution = $match[1];
                                    } else {
                                        $solution = 'ffrt';
                                    }
                                    $channel['cmd'] = $solution . ' http://' . $streamer . '/ch/' . $link_result . (empty($tmp_url_arr[4]) ? '' : $tmp_url_arr[4]) . (empty($tmp_url_arr[5]) ? '' : $tmp_url_arr[5]);
                                }
                            }
                        }
                    }
                }
            }
        }
        if (!empty($channel['streamer_id'])) {
            $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
            $cache->set($this->stb->id . '_playback', ['type' => 'tv-channel', 'id' => $channel['id'], 'link_id' => $channel['link_id'], 'streamer_id' => $channel['streamer_id']], 0, 10);
        } else {
            $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
            $cache->del($this->stb->id . '_playback');
        }
        return $channel;
    }
    private function getWowzaBalancer($url)
    {
        if (\preg_match('/:\\/\\/([^\\/]*)\\//', $url, $tmp)) {
            return $tmp[1];
        }
        return false;
    }
    private function getWowzaEdge($balancer_addr)
    {
        $a = \microtime(1);
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, $balancer_addr);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $responce = \curl_exec($ch);
        if ($responce === false) {
            return false;
        }
        $responce = \trim($responce);
        \var_dump($balancer_addr, 'load', \microtime(1) - $a);
        return \substr($responce, \strlen('redirect='));
    }
    private function createTemporaryLink($url)
    {
        $key = \md5($url . \microtime(1) . \uniqid());
        $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
        $result = $cache->set($key, $url, 0, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('tv_tmp_link_ttl', 5));
        if ($result) {
            return $key;
        }
        return $result;
    }
    public static function getEdgeCastAuthToken($token_type = 'EDGECAST_TV_SECURITY_TOKEN_TTL')
    {
        $params = 'ec_expire=' . (\time() + \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get($token_type)) . '&ec_clientip=' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->ip;
        $td = \mcrypt_module_open(MCRYPT_BLOWFISH, '', 'ncfb', '');
        $iv = "\0\0\0\0\0\0\0\0";
        $text = \sprintf('ec_secure=%03d&%s', \strlen($params) + 14, $params);
        $hash = false;
        if (\mcrypt_generic_init($td, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('EDGECAST_KEY'), $iv) != -1) {
            $cipherText = \mcrypt_generic($td, $text);
            \mcrypt_generic_deinit($td);
            $hash = \bin2hex($cipherText);
        }
        \mcrypt_module_close($td);
        return $hash;
    }
    public static function getAkamaiToken($token_type = 'AKAMAI_TV_SECURITY_TOKEN_TTL')
    {
        $acl = '/*';
        $field_delimiter = '~';
        $m_token = 'exp=' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get($token_type) . $field_delimiter;
        $m_token .= 'acl=' . \rawurlencode($acl) . $field_delimiter;
        $signature = \hash_hmac('SHA256', \rtrim($m_token, $field_delimiter), self::h2b(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('AKAMAI_KEY')));
        return 'hdnts=' . $m_token . 'hmac=' . $signature;
    }
    protected static function h2b($str)
    {
        $bin = '';
        $i = 0;
        do {
            $bin .= \chr(\hexdec($str[$i] . $str[$i + 1]));
            $i += 2;
        } while ($i < \strlen($str));
        return $bin;
    }
    public static function getNginxSecureLink($cmd)
    {
        if (\preg_match("/:\\/\\/([^\\/]+)\\/?(\\S*)/", $cmd, $match)) {
            $nginx_secure_link_order = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('nginx_secure_link_order');
            $nginx_secure_link_field = ['$secure_link_expires' => '', '$uri' => '', '$remote_addr' => '', '$secret' => ''];
            $path = '/' . $match[2];
            $expire = \time() + \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('nginx_secure_link_ttl', 5);
            if (\strpos($nginx_secure_link_order, '$secret') !== false) {
                $nginx_secure_link_field['$secret'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('nginx_secure_link_secret');
            }
            if (\strpos($nginx_secure_link_order, '$uri') !== false) {
                $url_path = \parse_url($path, PHP_URL_PATH);
                if (\substr(\strtolower($url_path), -5) == '.m3u8') {
                    $playlist = \substr($url_path, \strrpos($url_path, '/'));
                    $nginx_secure_link_field['$uri'] = \str_replace($playlist, '', $path);
                }
                $nginx_secure_link_field['$uri'] = $path;
            }
            if (\strpos($nginx_secure_link_order, '$secure_link_expires') !== false) {
                $nginx_secure_link_field['$secure_link_expires'] = $expire;
            }
            if (\strpos($nginx_secure_link_order, '$remote_addr') !== false) {
                if (!empty($_SERVER['REMOTE_ADDR'])) {
                    $nginx_secure_link_field['$remote_addr'] = $_SERVER['REMOTE_ADDR'];
                } else {
                    throw new \Ministra\Lib\ItvLinkException('link_fault');
                }
            }
            $hash = \base64_encode(\md5(\strtr($nginx_secure_link_order, $nginx_secure_link_field), true));
            $hash = \strtr($hash, '+/', '-_');
            $hash = \str_replace('=', '', $hash);
            $new_path = $path . (\strpos($cmd, '?') ? '&' : '?') . 'st=' . $hash . '&e=' . $expire;
            return \str_replace($match[1] . $path, $match[1] . $new_path, $cmd);
        }
        throw new \Ministra\Lib\ItvLinkException('link_fault');
    }
    public static function getNimbleHttpAuthToken($initial_url, $token_type = 'NIMBLE_TV_VALID_MINUTES')
    {
        $today = \gmdate('n/j/Y g:i:s A');
        $validminutes = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get($token_type);
        if ($pos = \strpos($initial_url, '?')) {
            $initial_url = \substr($initial_url, 0, $pos);
        }
        $tmp_arr = \explode('/', \trim($initial_url, '/'));
        $signed_stream = \count($tmp_arr) > 3 ? $tmp_arr[\count($tmp_arr) - 3] . '/' . $tmp_arr[\count($tmp_arr) - 2] : '';
        $str2hash = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->ip . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('NIMBLE_KEY') . $today . $validminutes . $signed_stream;
        $md5raw = \md5($str2hash, true);
        $base64hash = \base64_encode($md5raw);
        $urlsignature = 'server_time=' . $today . '&hash_value=' . $base64hash . "&validminutes={$validminutes}" . '&strm_len=' . \strlen($signed_stream);
        $base64urlsignature = \base64_encode($urlsignature);
        return "wmsAuthSign={$base64urlsignature}";
    }
    public static function getNimbleRtspAuthToken($initial_url, $token_type = 'NIMBLE_TV_VALID_MINUTES')
    {
        $today = \gmdate('n/j/Y g:i:s A');
        $validminutes = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get($token_type);
        if ($pos = \strpos($initial_url, '?')) {
            $initial_url = \substr($initial_url, 0, $pos);
        }
        $tmp_arr = \explode('/', \trim($initial_url, '/'));
        $video_url = '/' . \array_pop($tmp_arr);
        $str2hash = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->ip . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('NIMBLE_KEY') . $today . $validminutes;
        $md5raw = \md5($str2hash, true);
        $base64hash = \base64_encode($md5raw);
        $urlsignature = 'server_time=' . $today . '&hash_value=' . $base64hash . "&validminutes={$validminutes}";
        $base64urlsignature = \base64_encode($urlsignature);
        return "wmsAuthSign={$base64urlsignature}" . $video_url;
    }
    public static function getWowzaSecureToken($url, $endtime = null)
    {
        if (!\preg_match('/(\\S+)\\:\\/\\/(\\S+)/', $url, $match)) {
            return false;
        }
        $url = $match[1] . '://' . $match[2];
        $parts = \parse_url($url);
        $hashing_string = '';
        if (\strrpos(\strtolower($parts['path']), '.m3u8') == \strlen(\strtolower($parts['path'])) - \strlen('.m3u8')) {
            $path_parts = \explode('/', $parts['path'], -1);
            if ($path_parts) {
                unset($path_parts[0]);
            }
            $hashing_string .= \implode('/', $path_parts);
        } elseif (\strrpos(\strtolower($parts['path']), '/manifest') == \strlen(\strtolower($parts['path'])) - \strlen('/manifest')) {
            $path_parts = \explode('/', $parts['path'], -1);
            if ($path_parts) {
                unset($path_parts[0]);
            }
            $app = \array_shift($path_parts);
            \array_unshift($path_parts, $app, '_myInstance_');
            $hashing_string .= \implode('/', $path_parts);
        } elseif (\strpos(\strtolower($parts['scheme']), 'rtsp') === 0) {
            $path_parts = \explode('/', $parts['path']);
            if ($path_parts) {
                unset($path_parts[0]);
            }
            $app = \array_shift($path_parts);
            \array_unshift($path_parts, $app, '_myInstance_');
            $hashing_string .= \implode('/', $path_parts);
        } else {
            return false;
        }
        $hashing_string .= '?';
        $secret_hashing_params = [];
        $query_params = [];
        $prefix = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('wowza_securetoken_query_prefix', 'wowzatoken');
        if ($endtime === null) {
            $endtime = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('wowza_securetoken_tv_endtime', 0);
        }
        $shared_secret = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('wowza_securetoken_shared_secret', 'xyzSharedSecret');
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('wowza_securetoken_include_client_ip', false)) {
            $secret_hashing_params[] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->ip;
        }
        $secret_hashing_params[] = $shared_secret;
        $query_params[] = $prefix . 'endtime=' . ($endtime == 0 ? '0' : \time() + $endtime);
        $hashing_arr = \array_merge($query_params, $secret_hashing_params);
        \sort($hashing_arr);
        $hashing_string .= \implode('&', \array_merge($hashing_arr, []));
        $query_params[] = $prefix . 'hash=' . self::base64url_encode(\hash('sha256', $hashing_string, true));
        return \implode('&', $query_params);
    }
    private static function base64url_encode($data)
    {
        return \str_replace(['+', '/'], ['-', '_'], \base64_encode($data));
    }
    private function getFlexCDNToken($url)
    {
        $test_prefix = 'http://api.flex-cdn.net/';
        if (\strpos($url, $test_prefix) !== false) {
            $arrData = \explode('/', $url);
            $apiurl = 'http://' . $arrData[3] . '.' . $arrData[2] . '/api/';
            $serviceurl = 'hls://' . $arrData[4];
            $apireq = $apiurl . '?url=' . \urlencode($serviceurl);
            $i = 5;
            while ($i < \count($arrData)) {
                $apireq = $apireq . '/' . $arrData[$i];
                ++$i;
            }
            $apireq = $apireq . '&ip=' . \urlencode($this->stb->ip);
            $arrAPI = \json_decode(\file_get_contents($apireq), true);
            if ($arrAPI['code'] == 200) {
                $url = $arrAPI['url'];
            } else {
                throw new \Ministra\Lib\ItvLinkException('link_fault');
            }
        }
        return $url;
    }
    public function setPlayed($itv_id = null, $censored = null)
    {
        if ($itv_id === null) {
            $itv_id = @(int) $_REQUEST['itv_id'];
        }
        if ($censored === null) {
            $censored = @(int) $_REQUEST['censored'];
        }
        $this->db->insert('played_itv', ['itv_id' => $itv_id, 'uid' => $this->stb->id, 'playtime' => 'NOW()', 'user_locale' => $this->stb->R35cd2e80d7a2fc41598228f4269aed88('locale')]);
        $this->db->update('users', ['time_last_play_tv' => 'NOW()'], ['id' => $this->stb->id]);
        if (!$censored) {
            $this->setLastId($itv_id);
        }
        return true;
    }
    public function setLastId($id = 0)
    {
        if (!$id) {
            $id = (int) $_REQUEST['id'];
        }
        $last_id_arr = $this->db->from('last_id')->where(['uid' => $this->stb->id])->get()->first();
        if (!empty($last_id_arr) && \array_key_exists('last_id', $last_id_arr)) {
            $this->db->update('last_id', ['last_id' => $id], ['id' => $last_id_arr['id']]);
        } else {
            $this->db->insert('last_id', ['last_id' => $id, 'ident' => $this->stb->mac, 'uid' => $this->stb->id]);
        }
        return true;
    }
    public function setFav($uid = null)
    {
        if (!$uid) {
            $uid = $this->stb->id;
        }
        $fav_ch = @$_REQUEST['fav_ch'];
        if (empty($fav_ch)) {
            $fav_ch = [];
        } else {
            $fav_ch = \explode(',', $fav_ch);
        }
        if (\is_array($fav_ch)) {
            return $this->saveFav(\array_unique($fav_ch), $uid);
        }
        return true;
    }
    public function saveFav(array $fav_array, $uid)
    {
        if (empty($uid)) {
            return false;
        }
        $fav_ch_str = \base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($fav_array));
        if ($this->fav_itv === null) {
            $this->fav_itv = $fav_itv_arr = $this->db->from('fav_itv')->where(['uid' => (int) $uid])->use_caching(['fav_itv.uid=' . (int) $uid])->get()->first();
        } else {
            $fav_itv_arr = $this->fav_itv;
        }
        if (empty($fav_itv_arr)) {
            return $this->db->use_caching(['fav_itv.uid=' . (int) $uid])->insert('fav_itv', ['uid' => (int) $uid, 'fav_ch' => $fav_ch_str, 'addtime' => 'NOW()'])->insert_id();
        }
        return $this->db->use_caching(['fav_itv.uid=' . (int) $uid])->update('fav_itv', ['fav_ch' => $fav_ch_str, 'addtime' => 'NOW()'], ['uid' => (int) $uid])->result();
    }
    public function getAllChannels()
    {
        $result = $this->getChannels(true, true)->orderby('number');
        $this->include_censored = false;
        $this->setResponseData($result);
        return $this->getResponse('prepareData');
    }
    public function getChannels($include_censored = false, $include_unsubscribed = false)
    {
        $all_user_channels_ids = $this->getAllUserChannelsIds();
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tariff_plans', false)) {
            $user = \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
            $options = $user->getServicesByType('option');
            if ($options && \array_search('show_unsubscribed_tv_channels', $options) !== false) {
                $show_unsubscribed_tv_channels_option = true;
            } else {
                $show_unsubscribed_tv_channels_option = false;
            }
        } else {
            $show_unsubscribed_tv_channels_option = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_unsubscribed_tv_channels', false);
        }
        if (!$include_censored) {
            $censored_origin = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['censored' => 1])->get()->all('id');
            $censored_list = $this->getCensoredList();
            $censored_exclude_list = $this->getCensoredExcludeList();
            $censored_real = \array_values(\array_diff(\array_merge($censored_origin, $censored_list), $censored_exclude_list));
        }
        $query = $this->db->from('itv');
        $this->include_censored = $include_censored;
        if (!$include_censored) {
            $query->not_in('id', $censored_real);
        }
        if (!$this->stb->c6e0d92fc0ec62469764ba74feb893fa()) {
            $query->where(['status' => 1]);
        }
        if (!$include_unsubscribed || !$show_unsubscribed_tv_channels_option && (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_unsubscribed_tv_channels', false) || \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_unsubscribed_tv_channels', false) && \in_array($this->stb->mac, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('hide_unsubscribed_for_macs', [])))) {
            $query->in('id', $all_user_channels_ids);
        }
        return $query;
    }
    public function getAllUserChannelsIds()
    {
        if ($this->all_user_channels_ids === null) {
            $this->all_user_channels_ids = $this->getAllUserChannelsIdsByUid($this->stb->id);
        }
        return $this->all_user_channels_ids;
    }
    public function getAllUserChannelsIdsByUid($uid)
    {
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tariff_plans', false) && !\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tv_subscription_for_tariff_plans', false)) {
            $user = \Ministra\Lib\User::getInstance($uid);
            $subscription = $user->getServicesByType('tv', null, true);
            if (empty($subscription)) {
                $subscription = [];
            }
            if (\is_array($subscription)) {
                $this->user_channel_options = $subscription;
                $channel_ids = \array_keys($subscription);
            } else {
                $channel_ids = $subscription;
            }
        } else {
            $channel_ids = \array_unique(\array_merge(\Ministra\Lib\ItvSubscription::getSubscriptionChannelsIds($uid), \Ministra\Lib\ItvSubscription::getBonusChannelsIds($uid), $this->getBaseChannelsIds()));
        }
        $filtered_channels = self::getFilteredUserChannelsIds();
        if (!empty($_COOKIE['ext_channels']) && \in_array('ext_channels', \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::T4ec46d295e4a2ab17f5082fe27e3a5b7($this->stb->id))) {
            $ext_channels = \explode(',', $_COOKIE['ext_channels']);
            $ext_channels = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['bonus_ch' => 1])->in('id', $ext_channels)->get()->all('id');
            $channel_ids = \array_merge($channel_ids, $ext_channels);
        }
        if ($channel_ids == 'all') {
            $channel_ids = $filtered_channels;
        } else {
            $channel_ids = \array_intersect($channel_ids, $filtered_channels);
        }
        return $channel_ids;
    }
    public function getBaseChannelsIds()
    {
        $db = clone $this->db;
        return $db->from('itv')->where(['base_ch' => 1])->get()->all('id');
    }
    public static function getFilteredUserChannelsIds()
    {
        if (self::isDisabledChannelsByMac() || self::isDisabledChannelsBylogin()) {
            return [];
        }
        $all_links = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_links')->get()->all();
        $user_agent = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->e201dd29ac754599b321778554c12cc6();
        $user_links = self::filterLinksByUserAgentFilter($all_links, $user_agent);
        $user_ch_ids = \array_map(function ($link) {
            return $link['ch_id'];
        }, $user_links);
        $user_ch_ids = \array_unique($user_ch_ids);
        return $user_ch_ids;
    }
    private function getCensoredList()
    {
        if ($this->censored_channels === null) {
            $this->censored_channels = $list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('censored_channels')->where(['uid' => (int) $this->stb->id])->use_caching(['censored_channels.uid=' . (int) $this->stb->id])->get()->first();
        } else {
            $list = $this->censored_channels;
        }
        if (isset($list['list'])) {
            $list = $list['list'];
        } else {
            $list = [];
        }
        if (empty($list)) {
            return [];
        }
        $list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\Ministra\Lib\System::base64_decode($list));
        if ($list === false) {
            return [];
        }
        return $list;
    }
    private function getCensoredExcludeList()
    {
        if ($this->censored_channels === null) {
            $this->censored_channels = $list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('censored_channels')->where(['uid' => $this->stb->id])->get()->first();
        } else {
            $list = $this->censored_channels;
        }
        if (isset($list['exclude'])) {
            $list = $list['exclude'];
        } else {
            $list = [];
        }
        if (empty($list)) {
            return [];
        }
        $list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\Ministra\Lib\System::base64_decode($list));
        if ($list === false) {
            return [];
        }
        return $list;
    }
    public function getAllFavChannels()
    {
        $fav_ids = $this->getFav();
        $fav_str = \implode(',', $fav_ids);
        if (empty($fav_str)) {
            $fav_str = 'null';
        }
        $fav_channels = $this->getChannels(true, true)->in('id', $fav_ids)->orderby('field(id,' . $fav_str . ')');
        $this->include_censored = false;
        $this->setResponseData($fav_channels);
        return $this->getResponse('prepareData');
    }
    public function getFav($uid = null)
    {
        if (!$uid) {
            $uid = $this->stb->id;
        }
        if ($this->fav_itv === null) {
            $this->fav_itv = $fav_itv_ids_arr = $this->db->from('fav_itv')->where(['uid' => (int) $uid])->use_caching(['fav_itv.uid=' . (int) $uid])->get()->first();
        } else {
            $fav_itv_ids_arr = $this->fav_itv;
        }
        if (!empty($fav_itv_ids_arr)) {
            $fav_ch = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\base64_decode($fav_itv_ids_arr['fav_ch']));
            if (\is_array($fav_ch)) {
                return $fav_ch;
            }
        }
        return [];
    }
    public function getFavIds()
    {
        $fav = $this->getFav();
        $fav_str = \implode(',', $fav);
        if (empty($fav_str)) {
            $fav_str = 'null';
        }
        $fav_ids = $this->db->from('itv')->in('id', $fav)->where(['status' => 1])->orderby('field(id,' . $fav_str . ')')->get()->all('id');
        return $fav_ids;
    }
    public function getGenres()
    {
        $user_genres = $this->getChannels(true)->groupby('tv_genre_id')->get()->all('tv_genre_id');
        $genres_query = $this->db->from('tv_genre');
        if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_empty_tv_category', true)) {
            $genres_query->in('id', $user_genres);
        }
        $genres = $genres_query->orderby('number', 'ASC')->get()->all();
        if (\in_array('dvb', \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::T4ec46d295e4a2ab17f5082fe27e3a5b7($this->stb->id)) && \in_array(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('stb_type'), ['MAG270', 'MAG275', 'AuraHD3'])) {
            \array_unshift($genres, ['id' => 'dvb', 'title' => \_('DVB')]);
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_pvr_filter_in_genres_list', false) && \in_array(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('stb_type'), \explode(',', \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('allowed_stb_types_for_local_recording')))) {
            \array_unshift($genres, ['id' => 'pvr', 'title' => \_('Channels with PVR')]);
        }
        \array_unshift($genres, ['id' => '*', 'title' => $this->all_title]);
        $genres = \array_map(function ($item) {
            $item['alias'] = \strtolower($item['title']);
            $item['title'] = \_($item['title']);
            $item['censored'] = (int) $item['censored'];
            return $item;
        }, $genres);
        return $genres;
    }
    public function getOrderedList()
    {
        $fav = $this->getFav();
        $all_user_channels_ids = $this->getAllUserChannelsIds();
        $dvb_channels = $this->getDvbChannels();
        $fav_str = \implode(',', $fav);
        if (empty($fav_str)) {
            $fav_str = 'null';
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tariff_plans', false)) {
            $user = \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
            $options = $user->getServicesByType('option');
            if ($options && \array_search('show_unsubscribed_tv_channels', $options) !== false) {
                $show_unsubscribed_tv_channels_option = true;
            } else {
                $show_unsubscribed_tv_channels_option = false;
            }
            $packages = $user->getPackages();
            if (\is_array($packages)) {
                $packages = \array_map(function ($item) {
                    return $item['package_id'];
                }, $packages);
            }
        } else {
            $show_unsubscribed_tv_channels_option = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_unsubscribed_tv_channels', false);
        }
        $result = $this->getData();
        if (@$_REQUEST['sortby']) {
            $sortby = $_REQUEST['sortby'];
            if ($sortby == 'name') {
            } elseif ($sortby == 'number') {
                $result = $result->orderby('number');
            } elseif ($sortby == 'fav') {
                $result = $result->orderby('field(itv.id,' . $fav_str . ')');
            }
        } else {
            $result = $result->orderby('number');
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_numbering_in_order', false) && \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('order_itv_channel_as_adding', false) && \is_array($packages) && (empty($_REQUEST['sortby']) || !empty($_REQUEST['sortby']) && $_REQUEST['sortby'] == 'number')) {
            $result = $result->in('service_in_package.package_id', $packages);
        }
        if (@$_REQUEST['fav']) {
            $result = $result->in('itv.id', $fav);
        }
        if (!$show_unsubscribed_tv_channels_option && (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_unsubscribed_tv_channels', false) || \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_unsubscribed_tv_channels', false) && \in_array($this->stb->mac, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('hide_unsubscribed_for_macs', [])))) {
            $result = $result->in('itv.id', $all_user_channels_ids);
        }
        if (@$_REQUEST['sortby'] == 'name') {
            $iptv_channels = $result->nolimit()->get()->all();
            $iptv_channels_map = [];
            foreach ($iptv_channels as $channel) {
                $iptv_channels_map[$channel['name'] . '-' . $channel['id']] = $channel;
            }
            $dvb_channels_name_map = [];
            foreach ($dvb_channels as $channel) {
                $dvb_channels_name_map[$channel['name'] . '-' . $channel['id']] = $channel;
            }
            $all_channels_map = \array_merge($iptv_channels_map, $dvb_channels_name_map);
            \ksort($all_channels_map);
            $page_channels = \array_slice(\array_values($all_channels_map), $this->page * self::MAX_PAGE_ITEMS, self::MAX_PAGE_ITEMS);
            $this->setResponse('total_items', \count($all_channels_map));
            $this->setResponse('cur_page', $this->cur_page);
            $this->setResponse('selected_item', $this->selected_item);
            $this->setResponse('data', $page_channels);
        } else {
            $this->setResponseData($result);
        }
        return $this->getResponse('prepareData');
    }
    public function getDvbChannels()
    {
        $stb_type = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('stb_type');
        if ($stb_type != 'MAG270' && $stb_type != 'MAG275' && $stb_type != 'AuraHD3') {
            return [];
        }
        if ($this->dvb_channels !== null) {
            return $this->dvb_channels;
        }
        if (!\in_array('dvb', \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::T4ec46d295e4a2ab17f5082fe27e3a5b7($this->stb->id))) {
            $this->dvb_channels = [];
            return $this->dvb_channels;
        }
        $dvb_channels = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('dvb_channels')->where(['uid' => $this->stb->id])->get()->first('channels');
        if (empty($dvb_channels)) {
            $this->dvb_channels = [];
            return $this->dvb_channels;
        }
        $dvb_channels = \json_decode($dvb_channels, true);
        if (!$dvb_channels) {
            $this->dvb_channels = [];
            return $this->dvb_channels;
        }
        $ch_number = (int) $this->getChannels(true)->orderby('number', 'desc')->get()->first('number');
        $this->dvb_channels = \array_map(function ($channel) use(&$ch_number) {
            ++$ch_number;
            $channel['type'] = 'dvb';
            $channel['cmd'] = 'dvb dvb://' . $channel['id'];
            $channel['cmds'] = [$channel['cmd']];
            $channel['name'] = $channel['name'] . ' (DVB)';
            $channel['status'] = 1;
            $channel['number'] = (string) $ch_number;
            $channel['dvb_id'] = $channel['id'];
            $channel['id'] = (int) \str_replace(['T', 'C', '_'], '', $channel['id']);
            $channel['scrambled'] = $channel['scrambled'] == 'true' ? 1 : 0;
            $channel['tv_genre_id'] = 'dvb';
            unset($channel['isRadio'], $channel['symrate'], $channel['channel_number']);
            return $channel;
        }, $dvb_channels);
        return $this->dvb_channels;
    }
    private function getData()
    {
        $where = [];
        if (!$this->stb->c6e0d92fc0ec62469764ba74feb893fa()) {
            $where['status'] = 1;
        }
        if (@$_REQUEST['hd']) {
            $where['hd'] = 1;
        } else {
            $where['hd<='] = 1;
        }
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tv_quality_filter')) {
            $quality = empty($_REQUEST['quality']) ? $this->stb->R35cd2e80d7a2fc41598228f4269aed88('tv_quality') : $_REQUEST['quality'];
            $this->stb->x54edba767db8c04c1228188cc0931d55('tv_quality', $quality);
        }
        if (!empty($_REQUEST['genre']) && $_REQUEST['genre'] !== '*' && $_REQUEST['genre'] !== 'pvr') {
            $genre = (int) $_REQUEST['genre'];
            $where['tv_genre_id'] = $genre;
        } elseif (!empty($_REQUEST['genre']) && $_REQUEST['genre'] == 'pvr') {
            $where_or = ['allow_pvr' => 1, 'allow_local_pvr' => 1];
        }
        if ((empty($_REQUEST['genre']) || $_REQUEST['genre'] == '*') && !\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_adult_tv_channels_in_common_list', true)) {
            $where['tv_genre_id!='] = (int) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tv_genre')->where(['title' => 'for adults'])->get()->first('id');
        }
        $offset = $this->getOffset($where, isset($where_or) ? $where_or : []);
        $this->db->select(['itv.*'])->from('itv')->where($where)->limit(self::MAX_PAGE_ITEMS, $offset);
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_numbering_in_order', false) && \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('order_itv_channel_as_adding', false) && (empty($_REQUEST['sortby']) || !empty($_REQUEST['sortby']) && $_REQUEST['sortby'] == 'number')) {
            $this->db->join('service_in_package', 'itv.id', 'service_in_package.service_id', 'LEFT')->where(['service_in_package.type' => 'tv'])->orderby('service_in_package.id', 'ASC')->groupby('service_in_package.service_id');
        }
        if (isset($where_or)) {
            $this->db->where($where_or, 'OR ');
        }
        return $this->db;
    }
    private function getOffset($where = array(), $where_or = array())
    {
        if (!$this->load_last_page) {
            return $this->page * self::MAX_PAGE_ITEMS;
        }
        $fav = $this->getFav();
        $all_user_channels_ids = $this->getAllUserChannelsIds();
        if (!empty($_REQUEST['from_ch_id']) && (int) $_REQUEST['from_ch_id'] > 0) {
            $last_id = (int) $_REQUEST['from_ch_id'];
        } else {
            $last_id = $this->getLastId();
        }
        if (empty($_REQUEST['genre']) || $_REQUEST['genre'] == '*' || $_REQUEST['genre'] == 'dvb') {
            $dvb_channels = $this->getDvbChannels();
        } else {
            $dvb_channels = [];
        }
        $tv_number = $this->db->from('itv')->where(['id' => $last_id])->get()->first('number');
        if (empty($tv_number) && !empty($dvb_channels)) {
            foreach ($dvb_channels as $channel) {
                if ($channel['id'] == $last_id) {
                    $tv_number = $channel['number'];
                    break;
                }
            }
        }
        $ch_idx = 0;
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tariff_plans', false)) {
            $user = \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
            $options = $user->getServicesByType('option');
            if ($options && \array_search('show_unsubscribed_tv_channels', $options) !== false) {
                $show_unsubscribed_tv_channels_option = true;
            } else {
                $show_unsubscribed_tv_channels_option = false;
            }
        } else {
            $show_unsubscribed_tv_channels_option = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_unsubscribed_tv_channels', false);
        }
        if (@$_REQUEST['fav']) {
            if (\in_array($last_id, $fav)) {
                $ch_tmp_idx = \array_search($last_id, $fav);
                if ($ch_tmp_idx >= 0) {
                    $fav = \array_slice($fav, 0, $ch_tmp_idx + 1);
                }
                $query = $this->db->from('itv')->where($where)->in('itv.id', $fav);
                if (!empty($where_or)) {
                    $query->where($where_or, 'OR ');
                }
                if (!$show_unsubscribed_tv_channels_option && (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_unsubscribed_tv_channels', false) || \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_unsubscribed_tv_channels', false) && \in_array($this->stb->mac, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('hide_unsubscribed_for_macs', [])))) {
                    $query->in('itv.id', $all_user_channels_ids);
                }
                $ch_idx = $query->get()->count();
                if (!empty($dvb_channels)) {
                    $flipped_fav = \array_flip($fav);
                    foreach ($dvb_channels as $channel) {
                        if (isset($flipped_fav[$channel['id']])) {
                            ++$ch_idx;
                        }
                    }
                }
            }
        } else {
            $sortby = $_REQUEST['sortby'];
            if ($sortby == 'name') {
                $query = $this->db->from('itv')->where($where)->orderby('name');
                if (!empty($where_or)) {
                    $query->where($where_or, 'OR ');
                }
                if (!$show_unsubscribed_tv_channels_option && (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_unsubscribed_tv_channels', false) || \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_unsubscribed_tv_channels', false) && \in_array($this->stb->mac, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('hide_unsubscribed_for_macs', [])))) {
                    $query->in('itv.id', $all_user_channels_ids);
                }
                $all_channels = $query->get()->all();
                $all_channels_map = [];
                foreach ($all_channels as $channel) {
                    $all_channels_map[$channel['name'] . '-' . $channel['id']] = $channel;
                }
                $dvb_channels_name_map = [];
                foreach ($dvb_channels as $channel) {
                    $dvb_channels_name_map[$channel['name'] . '-' . $channel['id']] = $channel;
                }
                $all_channels_map = \array_merge($all_channels_map, $dvb_channels_name_map);
                \ksort($all_channels_map);
                $ch_idx = 0;
                foreach ($all_channels_map as $key => $channel) {
                    ++$ch_idx;
                    if ($channel['id'] == $last_id) {
                        break;
                    }
                }
            } else {
                $query = $this->db->from('itv')->where($where)->where(['number<=' => $tv_number]);
                if (!empty($where_or)) {
                    $query->where($where_or, 'OR ');
                }
                if (!$show_unsubscribed_tv_channels_option && (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_unsubscribed_tv_channels', false) || \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_unsubscribed_tv_channels', false) && \in_array($this->stb->mac, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('hide_unsubscribed_for_macs', [])))) {
                    $query->in('itv.id', $all_user_channels_ids);
                }
                $all_ids = $query->get()->all('id');
                if (!empty($dvb_channels) && $dvb_channels[0]['number'] < $tv_number) {
                    $dvb_channels_ids = [];
                    foreach ($dvb_channels as $channel) {
                        if ($channel['number'] <= $tv_number) {
                            $dvb_channels_ids[] = $channel['id'];
                        }
                    }
                    $all_ids = \array_merge($all_ids, $dvb_channels_ids);
                }
                if (\array_search($last_id, $all_ids) !== false) {
                    $ch_idx = \count($all_ids);
                } else {
                    $ch_idx = 1;
                }
            }
        }
        if ($ch_idx > 0) {
            $this->cur_page = \ceil($ch_idx / self::MAX_PAGE_ITEMS);
            $this->page = $this->cur_page - 1;
            $this->selected_item = $ch_idx - ($this->cur_page - 1) * self::MAX_PAGE_ITEMS;
        }
        $page_offset = ($this->cur_page - 1) * self::MAX_PAGE_ITEMS;
        if ($page_offset < 0) {
            $page_offset = 0;
        }
        return $page_offset;
    }
    public function getLastId()
    {
        $last_id_arr = $this->db->from('last_id')->where(['uid' => $this->stb->id])->get()->first();
        if (!empty($last_id_arr) && \array_key_exists('last_id', $last_id_arr)) {
            return $last_id_arr['last_id'];
        }
        return 0;
    }
    public function getEpgInfo()
    {
        $epg = new \Ministra\Lib\Epg();
        $period = empty($_REQUEST['period']) ? 3 : (int) $_REQUEST['period'];
        $response = ['data' => $epg->getEpgInfo($period)];
        return $response;
    }
    public function setClaim()
    {
        return $this->setClaimGlobal('itv');
    }
    public function setFavStatus()
    {
        return $this->db->update('users', ['fav_itv_on' => @(int) $_REQUEST['fav_itv_on']], ['id' => $this->stb->id]);
    }
    public function getShortEpg()
    {
        $ch_id = (int) $_REQUEST['ch_id'];
        $channel = \Ministra\Lib\Itv::getById($ch_id);
        if (empty($channel['xmltv_id'])) {
            return [];
        }
        $epg = new \Ministra\Lib\Epg();
        if ($_REQUEST['size']) {
            return $epg->getCurProgramAndFewNext($ch_id, (int) $_REQUEST['size']);
        }
        return $epg->getCurProgramAndFiveNext($ch_id);
    }
    public function addToCensored()
    {
        $ch_id = (int) $_REQUEST['ch_id'];
        $censored_list = $this->getCensoredList();
        \array_push($censored_list, $ch_id);
        return $this->setCensoredList(\array_unique($censored_list));
    }
    private function setCensoredList($list)
    {
        if ($this->censored_channels === null) {
            $this->censored_channels = $item = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('censored_channels')->where(['uid' => (int) $this->stb->id])->use_caching(['censored_channels.uid=' . (int) $this->stb->id])->get()->first();
        } else {
            $item = $this->censored_channels;
        }
        $data = ['list' => \Ministra\Lib\System::base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($list)), 'uid' => $this->stb->id];
        if (empty($item)) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('censored_channels', $data)->insert_id();
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->use_caching()->update('censored_channels', $data, ['uid' => (int) $this->stb->id]);
    }
    public function delFromCensored()
    {
        $ch_id = (int) $_REQUEST['ch_id'];
        $censored_list = $this->getCensoredList();
        $idx = \array_search($ch_id, $censored_list);
        if ($idx === false) {
            $exclude_list = $this->getCensoredExcludeList();
            \array_push($exclude_list, $ch_id);
            return $this->setCensoredExcludeList(\array_unique($exclude_list));
        }
        unset($censored_list[$idx]);
        return $this->setCensoredList($censored_list);
    }
    private function setCensoredExcludeList($list)
    {
        if ($this->censored_channels === null) {
            $this->censored_channels = $item = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('censored_channels')->where(['uid' => $this->stb->id])->get()->first();
        } else {
            $item = $this->censored_channels;
        }
        $data = ['exclude' => \Ministra\Lib\System::base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($list)), 'uid' => $this->stb->id];
        if (empty($item)) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('censored_channels', $data)->insert_id();
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('censored_channels', $data, ['uid' => $this->stb->id]);
    }
    public function saveDvbChannels()
    {
        $channels = \json_decode($_REQUEST['channels'], true);
        if ($channels === null) {
            return false;
        }
        $dvb_channels = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('dvb_channels')->where(['uid' => $this->stb->id])->get()->first('channels');
        if (empty($dvb_channels)) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('dvb_channels', ['uid' => $this->stb->id, 'channels' => \json_encode($channels), 'modified' => 'NOW()'])->insert_id();
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('dvb_channels', ['channels' => \json_encode($channels), 'modified' => 'NOW()'], ['uid' => $this->stb->id])->result();
    }
    public function getUrlByChannelId($ch_id, $link_id = null)
    {
        $channel = $this->getRealChannelByChannelId($ch_id, $link_id);
        return empty($channel['cmd']) ? false : $channel['cmd'];
    }
    public function getListByNumber()
    {
        $page = (int) $_REQUEST['p'];
        $this->db->from('itv')->where(['status' => 1]);
    }
    public function prepareData()
    {
        $fav = $this->getFav();
        $censored_list = $this->getCensoredList();
        $censored_exclude_list = $this->getCensoredExcludeList();
        $dvb_channels = $this->getDvbChannels();
        $force_ch_link_check = isset($_REQUEST['force_ch_link_check']) ? $_REQUEST['force_ch_link_check'] : false;
        $epg = new \Ministra\Lib\Epg();
        $quality = $this->stb->R35cd2e80d7a2fc41598228f4269aed88('tv_quality');
        $total_iptv_channels = (int) $this->response['total_items'];
        if (!empty($_REQUEST['fav'])) {
            $dvb_channels = \array_values(\array_filter($dvb_channels, function ($channel) use($fav) {
                return \in_array($channel['id'], $fav);
            }));
        }
        if (@$_REQUEST['sortby'] != 'name' && (empty($_REQUEST['genre']) || $_REQUEST['genre'] == '*' || $_REQUEST['genre'] == 'dvb')) {
            $this->response['total_items'] += \count($dvb_channels);
        }
        if ((\count($this->response['data']) < self::MAX_PAGE_ITEMS && !empty($dvb_channels) || !isset($_REQUEST['p'])) && @$_REQUEST['sortby'] != 'name' && (empty($_REQUEST['genre']) || $_REQUEST['genre'] == '*' || $_REQUEST['genre'] == 'dvb')) {
            $total_iptv_pages = \ceil($total_iptv_channels / self::MAX_PAGE_ITEMS);
            if ($this->page == $total_iptv_pages - 1) {
                $dvb_part_length = self::MAX_PAGE_ITEMS - $total_iptv_channels % self::MAX_PAGE_ITEMS;
            } else {
                $dvb_part_length = self::MAX_PAGE_ITEMS;
            }
            if (!empty($_REQUEST['genre']) && $_REQUEST['genre'] == 'dvb') {
                $dvb_part_offset = $this->page * self::MAX_PAGE_ITEMS;
            } elseif ($this->page + 1 > $total_iptv_pages) {
                $diff_items = $total_iptv_channels % self::MAX_PAGE_ITEMS;
                $dvb_part_offset = ($this->page - $total_iptv_pages) * self::MAX_PAGE_ITEMS + ($diff_items > 0 ? self::MAX_PAGE_ITEMS - $diff_items : 0);
            } else {
                $dvb_part_offset = 0;
            }
            if (isset($_REQUEST['p'])) {
                $dvb_channels = \array_splice($dvb_channels, $dvb_part_offset, $dvb_part_length);
            }
            $this->response['data'] = \array_merge($this->response['data'], $dvb_channels);
            if (!empty($_REQUEST['fav'])) {
                $ordered_list = [];
                $channels_map = [];
                foreach ($this->response['data'] as $channel) {
                    $channels_map[$channel['id']] = $channel;
                }
                foreach ($fav as $ch_id) {
                    if (!empty($channels_map[$ch_id])) {
                        $ordered_list[] = $channels_map[$ch_id];
                    }
                }
                $this->response['data'] = $ordered_list;
            }
        }
        $length = \count($this->response['data']);
        $enable_numbering_in_order = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_numbering_in_order', false);
        $excluded = 0;
        $ch_ids = [];
        for ($i = 0; $i < $length; ++$i) {
            if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tv_quality_filter')) {
                if ($quality == 'low') {
                    if ($this->response['data'][$i]['cmd_3']) {
                        $this->response['data'][$i]['cmd'] = $this->response['data'][$i]['cmd_3'];
                        $this->response['data'][$i]['quality_low'] = 1;
                    } else {
                        if ($this->response['data'][$i]['cmd_2']) {
                            $this->response['data'][$i]['cmd'] = $this->response['data'][$i]['cmd_2'];
                            $this->response['data'][$i]['quality_medium'] = 1;
                        } else {
                            $this->response['data'][$i]['cmd'] = $this->response['data'][$i]['cmd_1'];
                            $this->response['data'][$i]['quality_high'] = 1;
                        }
                    }
                } else {
                    if ($quality == 'medium') {
                        if ($this->response['data'][$i]['cmd_2']) {
                            $this->response['data'][$i]['cmd'] = $this->response['data'][$i]['cmd_2'];
                            $this->response['data'][$i]['quality_medium'] = 1;
                        } else {
                            if ($this->response['data'][$i]['cmd_3']) {
                                $this->response['data'][$i]['cmd'] = $this->response['data'][$i]['cmd_3'];
                                $this->response['data'][$i]['quality_low'] = 1;
                            } else {
                                $this->response['data'][$i]['cmd'] = $this->response['data'][$i]['cmd_1'];
                                $this->response['data'][$i]['quality_high'] = 1;
                            }
                        }
                    } else {
                        if ($this->response['data'][$i]['cmd_1']) {
                            $this->response['data'][$i]['cmd'] = $this->response['data'][$i]['cmd_1'];
                            $this->response['data'][$i]['quality_high'] = 1;
                        } else {
                            if ($this->response['data'][$i]['cmd_2']) {
                                $this->response['data'][$i]['cmd'] = $this->response['data'][$i]['cmd_2'];
                                $this->response['data'][$i]['quality_medium'] = 1;
                            } else {
                                $this->response['data'][$i]['cmd'] = $this->response['data'][$i]['cmd_3'];
                                $this->response['data'][$i]['quality_low'] = 1;
                            }
                        }
                    }
                }
            }
            if ($this->response['data'][$i]['censored'] && !\in_array($this->response['data'][$i]['id'], $censored_exclude_list)) {
                $this->response['data'][$i]['lock'] = 1;
            } else {
                $this->response['data'][$i]['lock'] = 0;
            }
            if (\in_array($this->response['data'][$i]['id'], $censored_list)) {
                $this->response['data'][$i]['lock'] = 1;
            }
            unset($this->response['data'][$i]['descr'], $this->response['data'][$i]['monitoring_url']);
            if ($this->response['data'][$i]['lock'] == 1 && !$this->include_censored) {
                \array_splice($this->response['data'], $i, 1);
                --$length;
                --$i;
                ++$excluded;
                continue;
            }
            if (\in_array($this->response['data'][$i]['id'], $fav)) {
                $this->response['data'][$i]['fav'] = 1;
            } else {
                $this->response['data'][$i]['fav'] = 0;
            }
            if ($this->response['data'][$i]['tv_archive_type'] && (!isset($this->user_channel_options[$this->response['data'][$i]['id']]['enable_tv_archive']) || $this->user_channel_options[$this->response['data'][$i]['id']]['enable_tv_archive'] == 1)) {
                $this->response['data'][$i]['archive'] = 1;
            } else {
                $this->response['data'][$i]['archive'] = 0;
            }
            $this->response['data'][$i]['enable_tv_archive'] = (int) (!empty($this->response['data'][$i]['tv_archive_type']));
            if (@$_REQUEST['fav'] || $enable_numbering_in_order) {
                $this->response['data'][$i]['number'] = (string) ($i + 1 + self::MAX_PAGE_ITEMS * $this->page + (!empty($_REQUEST['fav']) || $enable_numbering_in_order ? $excluded : 0));
            }
            $this->response['data'][$i]['genres_str'] = '';
            $this->response['data'][$i]['epg'] = empty($next_five_epg) ? [] : $next_five_epg;
            $this->response['data'][$i]['open'] = 1;
            if ($this->response['data'][$i]['use_http_tmp_link'] || \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('force_ch_link_check', false) || $force_ch_link_check) {
                $this->response['data'][$i]['cmd'] = 'ffrt http://' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('stream_proxy') . '/ch/' . $this->response['data'][$i]['id'];
            }
            if ($this->response['data'][$i]['enable_wowza_load_balancing']) {
                $this->response['data'][$i]['use_http_tmp_link'] = 1;
                $this->response['data'][$i]['cmd'] = 'udp://ch/' . $this->response['data'][$i]['id'];
            }
            if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_subscription') && (empty($this->response['data'][$i]['type']) || $this->response['data'][$i]['type'] != 'dvb')) {
                if (\in_array($this->response['data'][$i]['id'], $this->getAllUserChannelsIds()) || $this->stb->c6e0d92fc0ec62469764ba74feb893fa()) {
                    $this->response['data'][$i]['open'] = 1;
                } else {
                    $this->response['data'][$i]['open'] = 0;
                    $this->response['data'][$i]['cmd'] = 'udp://wtf?';
                }
            }
            if ($this->response['data'][$i]['status'] == 0 && $this->stb->c6e0d92fc0ec62469764ba74feb893fa()) {
                $this->response['data'][$i]['only_for_moderator'] = 1;
            }
            $ch_ids[] = $this->response['data'][$i]['id'];
            $this->response['data'][$i]['mc_cmd'] = empty($this->response['data'][$i]['mc_cmd']) ? '' : '1';
            $this->response['data'][$i]['allow_pvr'] = $this->response['data'][$i]['allow_pvr'] == 0 ? '' : '1';
            $this->response['data'][$i]['allow_local_pvr'] = $this->response['data'][$i]['allow_local_pvr'] == 0 ? '' : '1';
            $this->response['data'][$i]['pvr'] = (int) (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('show_tv_channel_pvr_icon', true) && ($this->response['data'][$i]['allow_pvr'] || $this->response['data'][$i]['allow_local_pvr']));
        }
        $cur_programs = $epg->getCurProgramsMap($ch_ids);
        $urls_map = $this->getUrlsMapForChannels($ch_ids, $force_ch_link_check);
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $cur_program = isset($cur_programs[$this->response['data'][$i]['id']]) ? $cur_programs[$this->response['data'][$i]['id']] : null;
            if (!empty($cur_program)) {
                $cur_playing = $cur_program['t_time'] . ' ' . $cur_program['name'];
            } else {
                $cur_playing = $this->no_ch_info;
            }
            $this->response['data'][$i]['cur_playing'] = $cur_playing;
            if (empty($this->response['data'][$i]['type']) || $this->response['data'][$i]['type'] != 'dvb') {
                $this->response['data'][$i]['cmds'] = isset($urls_map[$this->response['data'][$i]['id']]) ? $urls_map[$this->response['data'][$i]['id']] : [];
                $this->response['data'][$i]['cmd'] = empty($this->response['data'][$i]['cmds'][0]['url']) ? '' : $this->response['data'][$i]['cmds'][0]['url'];
                $this->response['data'][$i]['use_http_tmp_link'] = empty($this->response['data'][$i]['cmds'][0]['use_http_tmp_link']) ? 0 : $this->response['data'][$i]['cmds'][0]['use_http_tmp_link'];
                $this->response['data'][$i]['wowza_tmp_link'] = empty($this->response['data'][$i]['cmds'][0]['wowza_tmp_link']) ? 0 : $this->response['data'][$i]['cmds'][0]['wowza_tmp_link'];
                $this->response['data'][$i]['use_load_balancing'] = empty($this->response['data'][$i]['cmds'][0]['use_load_balancing']) ? 0 : $this->response['data'][$i]['cmds'][0]['use_load_balancing'];
            }
            if (empty($this->response['data'][$i]['cmds']) || $this->response['data'][$i]['enable_monitoring'] && $this->response['data'][$i]['monitoring_status'] == 0) {
                $this->response['data'][$i]['open'] = 0;
                $this->response['data'][$i]['error'] = 'limit';
                $this->response['data'][$i]['cmd'] = 'udp://wtf?';
            }
        }
        return $this->response;
    }
    public function getUrlsMapForChannels($ch_ids, $force_ch_link_check = false)
    {
        if (self::isDisabledChannelsByMac() || self::isDisabledChannelsBylogin()) {
            return [];
        }
        $user_agent = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->e201dd29ac754599b321778554c12cc6();
        $channel_links = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('ch_links')->where(['status' => 1])->in('ch_id', $ch_ids)->orderby('priority, rand()')->get()->all();
        $user_channel_links = \array_map(function ($link) use($force_ch_link_check) {
            if ($link['use_http_tmp_link'] == 1 || $link['use_load_balancing'] == 1 || \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('force_ch_link_check', false) || $force_ch_link_check) {
                if (\preg_match("/(\\w+)\\s+http:/", $link['url'], $match)) {
                    $solution = $match[1];
                } else {
                    $solution = 'ffrt';
                }
                $link['url'] = $solution . ' http://' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('stream_proxy') . '/ch/' . $link['id'];
            }
            unset($link['monitoring_url']);
            return $link;
        }, $channel_links);
        $grouped_links = [];
        foreach ($user_channel_links as $link) {
            if (!isset($grouped_links[$link['ch_id']])) {
                $grouped_links[$link['ch_id']] = [];
            }
            $grouped_links[$link['ch_id']][] = $link;
        }
        foreach ($grouped_links as $ch_id => $links) {
            $user_channel_links = self::filterLinksByUserAgentFilter($links, $user_agent);
            if (empty($user_channel_links)) {
                $user_channel_links = \array_filter($links, function ($link) {
                    return $link['user_agent_filter'] == '';
                });
            }
            $grouped_links[$ch_id] = \array_values($user_channel_links);
        }
        return $grouped_links;
    }
    public function getUserChannelOptions()
    {
        return $this->user_channel_options;
    }
    public function getChannelsByIds($ids)
    {
        return $this->getChannels(true)->in('id', $ids)->get()->all();
    }
    public function getByIds($ids = array())
    {
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv');
        if (!empty($ids)) {
            $result = $result->in('id', $ids);
        }
        $result = $result->get()->all();
        return $result;
    }
    public function getRawAllUserChannels($uid = null)
    {
        if ($uid) {
            $user_channels = $this->getAllUserChannelsIdsByUid($uid);
            $orderby = 'number';
            if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_numbering_in_order', false) && \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('order_itv_channel_as_adding', false) && (empty($_REQUEST['sortby']) || !empty($_REQUEST['sortby']) && $_REQUEST['sortby'] == 'number') && !empty($user_channels)) {
                $orderby = ' field( id, ' . \implode(', ', $user_channels) . ')';
            }
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['status' => 1])->in('itv.id', $user_channels)->orderby($orderby);
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['itv.status' => 1])->orderby('number');
    }
    public function getLinksForMonitoring($status = false)
    {
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('ch_links.*, itv.name as ch_name')->from('ch_links')->join('itv', 'itv.id', 'ch_links.ch_id', 'INNER')->where(['ch_links.enable_monitoring' => 1, 'ch_links.enable_balancer_monitoring' => 0]);
        if ($status) {
            $result->where(['ch_links.status' => (int) ($status == 'up')]);
        }
        $monitoring_links = $result->orderby('ch_id')->get()->all();
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('ch_links.*, streamer_id, ch_link_on_streamer.id as streamer_link_id, itv.name as ch_name')->from('ch_links')->join('ch_link_on_streamer', 'link_id', 'ch_links.id', 'INNER')->join('itv', 'itv.id', 'ch_links.ch_id', 'INNER')->where(['ch_links.enable_monitoring' => 1, 'ch_links.enable_balancer_monitoring' => 1, 'ch_links.use_load_balancing' => 1]);
        if ($status) {
            $result->where(['ch_links.status' => (int) ($status == 'up')]);
        }
        $balanser_monitoring_links_raw = $result->orderby('ch_id')->get()->all();
        $servers_map = \Ministra\Lib\StreamServer::getIdMap();
        $balanser_monitoring_links = [];
        foreach ($balanser_monitoring_links_raw as $link) {
            if (empty($servers_map[$link['streamer_id']])) {
                continue;
            }
            if ($link['use_http_tmp_link'] == 1 && $link['wowza_tmp_link'] == 0) {
                $colon_pos = \strpos($servers_map[$link['streamer_id']]['address'], ':');
                if ($colon_pos === false) {
                    $address = $servers_map[$link['streamer_id']]['address'];
                } else {
                    $address = \substr($servers_map[$link['streamer_id']]['address'], 0, $colon_pos);
                }
                $link['url'] = \preg_replace('/:\\/\\/([^\\/:]*)/', '://' . $address, $link['url']);
                $link['monitoring_url'] = \preg_replace('/:\\/\\/([^\\/:]*)/', '://' . $address, $link['monitoring_url']);
            } else {
                $link['url'] = \preg_replace('/:\\/\\/([^\\/]*)/', '://' . $servers_map[$link['streamer_id']]['address'], $link['url']);
                $link['monitoring_url'] = \preg_replace('/:\\/\\/([^\\/]*)/', '://' . $servers_map[$link['streamer_id']]['address'], $link['monitoring_url']);
            }
            $link['id'] = 's' . $link['streamer_link_id'];
            $balanser_monitoring_links[] = $link;
        }
        $monitoring_links = \array_merge($monitoring_links, $balanser_monitoring_links);
        $monitoring_links = \array_map(function ($cmd) {
            $cmd['monitoring_url'] = \trim($cmd['monitoring_url']);
            if (!empty($cmd['monitoring_url']) && \preg_match("/(\\S+:\\/\\/\\S+)/", $cmd['monitoring_url'], $match)) {
                $cmd['url'] = $match[1];
            } else {
                if (\preg_match("/(\\S+:\\/\\/\\S+)/", $cmd['url'], $match)) {
                    $cmd['url'] = $match[1];
                }
            }
            if ($cmd['flussonic_tmp_link'] && \strpos($cmd['url'], 'http') === 0) {
                $cmd['type'] = 'flussonic_health';
            } elseif ($cmd['nginx_secure_link']) {
                try {
                    $cmd['type'] = 'nginx_secure_link';
                    $cmd['url'] = \Ministra\Lib\Itv::getNginxSecureLink($cmd['url']);
                } catch (\Ministra\Lib\ItvLinkException $e) {
                    return false;
                }
            } else {
                $cmd['type'] = 'stream';
            }
            return $cmd;
        }, $monitoring_links);
        return \array_values(\array_filter($monitoring_links));
    }
    private function getGenreById($id)
    {
        $genre = $this->db->from('tv_genre')->where(['id' => $id])->get()->first();
        if (empty($genre)) {
            return '';
        }
        return $genre['title'];
    }
}
