<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class Radio extends \Ministra\Lib\AjaxResponse implements \Ministra\Lib\StbApi\Radio
{
    public static $instance = null;
    public $fav_radio = false;
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
    public static function getById($id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('radio')->where(['status' => 1, 'id' => $id])->get()->first();
    }
    public static function getServices()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('id, name')->from('radio')->get()->all();
    }
    public static function setChannelLinkStatus($link_id, $status)
    {
        if (empty($link_id) || !\is_numeric($link_id)) {
            return false;
        }
        $channel = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('radio')->where(['id' => $link_id])->get()->first();
        if (empty($channel)) {
            return false;
        }
        if ((int) $status != (int) $channel['monitoring_status']) {
            if ((int) $status == 0) {
                if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('administrator_email')) {
                    $message = \sprintf(\_('Radio-channel %s set to active because its URL became available.'), $channel['number'] . ' ' . $channel['name']);
                    \mail(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('administrator_email'), 'Radio-channels monitoring report: channel enabled', $message, "Content-type: text/html; charset=UTF-8\r\n");
                }
            } else {
                if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('administrator_email')) {
                    $message = \sprintf(\_('Radio-channel %s set to inactive because its URL are not available.'), $channel['number'] . ' ' . $channel['name']);
                    \mail(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('administrator_email'), 'Radio-channels monitoring report: channel disabled', $message, "Content-type: text/html; charset=UTF-8\r\n");
                }
            }
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('radio', ['monitoring_status' => $status], ['id' => $link_id])->result();
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('radio', ['monitoring_status_updated' => 'NOW()'], ['id' => $link_id])->result();
    }
    public function getOrderedList()
    {
        $user = \Ministra\Lib\User::getInstance($this->stb->id);
        $all_user_radio_ids = $user->getServicesByType('radio');
        if ($all_user_radio_ids === null) {
            $all_user_radio_ids = [];
        }
        if ($this->getFav($this->stb->id) !== false) {
            $fav_str = \implode(',', $this->fav_radio);
        } else {
            $fav_str = 'null';
        }
        $result = $this->getData();
        if (@$_REQUEST['search']) {
            $search = \trim($_REQUEST['search']);
            $result = $result->like(['name' => "%{$search}%"]);
        }
        if (@$_REQUEST['sortby']) {
            $sortby = $_REQUEST['sortby'];
            if ($sortby == 'name') {
                $result = $result->orderby('name');
            } elseif ($sortby == 'number') {
                $result = $result->orderby('number');
            } elseif ($sortby == 'fav') {
                $result = $result->orderby('field(id,' . $fav_str . ')');
            }
        } else {
            $result = $result->orderby('number');
        }
        if (@$_REQUEST['fav']) {
            $result = $result->in('radio.id', $this->fav_radio !== false ? $this->fav_radio : []);
        }
        $result = $result->orderby('number');
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans') && $all_user_radio_ids != 'all') {
            $result = $result->in('radio.id', $all_user_radio_ids);
        }
        $this->setResponseData($result);
        return $this->getResponse('prepareData');
    }
    public function getFav($uid = null)
    {
        if (!$uid) {
            $uid = $this->stb->id;
        }
        if ($this->fav_radio === false) {
            $fav_radio_ids_arr = $this->db->select('fav_radio')->from('fav_radio')->where(['uid' => (int) $uid])->use_caching(['fav_radio.uid=' . (int) $uid])->get()->first('fav_radio');
            if (!empty($fav_radio_ids_arr)) {
                $this->fav_radio = \is_string($fav_radio_ids_arr) ? \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($fav_radio_ids_arr) : false;
            }
        }
        return $this->fav_radio;
    }
    private function getData()
    {
        $offset = $this->page * self::MAX_PAGE_ITEMS;
        $where = [];
        if (!$this->stb->c6e0d92fc0ec62469764ba74feb893fa()) {
            $where['status'] = 1;
        }
        $this->db->from('radio')->where($where);
        if (empty($_REQUEST['all'])) {
            $this->db->limit(self::MAX_PAGE_ITEMS, $offset);
        }
        return $this->db;
    }
    public function setFav($uid = null)
    {
        if (!$uid) {
            $uid = $this->stb->id;
        }
        $fav_radio = @$_REQUEST['fav_radio'];
        if (empty($fav_radio)) {
            $fav_radio = [];
        } else {
            $fav_radio = \explode(',', $fav_radio);
        }
        if (\is_array($fav_radio)) {
            return $this->saveFav(\array_unique($fav_radio), $uid);
        }
        return true;
    }
    public function saveFav(array $fav_array, $uid)
    {
        if (empty($uid)) {
            return false;
        }
        $fav_ch_str = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($fav_array);
        if (empty($this->fav_radio)) {
            $this->getFav($uid);
        }
        if ($this->fav_radio === false) {
            return $this->db->use_caching(['fav_radio.uid=' . (int) $uid])->insert('fav_radio', ['uid' => (int) $uid, 'fav_radio' => $fav_ch_str, 'addtime' => 'NOW()'])->insert_id();
        }
        return $this->db->use_caching(['fav_radio.uid=' . (int) $uid])->update('fav_radio', ['fav_radio' => $fav_ch_str], ['uid' => (int) $uid])->result();
    }
    public function getAllFavRadio()
    {
        if ($this->getFav() !== false && !empty($this->fav_radio)) {
            $fav_str = \implode(',', $this->fav_radio);
        } else {
            $fav_str = 'null';
        }
        $fav_radios = $this->db->from('radio')->in('id', $this->fav_radio !== false ? $this->fav_radio : [])->where(['status' => 1])->orderby('field(id,' . $fav_str . ')');
        $this->setResponseData($fav_radios);
        return $this->getResponse('prepareData');
    }
    public function setFavStatus()
    {
    }
    public function prepareData()
    {
        if (\is_array($this->response['data'])) {
            $fav_ids = $this->getFavIds();
            $counter = 1;
            $delimiter = self::MAX_PAGE_ITEMS;
            $this->response['data'] = \array_map(function ($row) use($fav_ids, &$counter, $delimiter) {
                $row['fav'] = (int) \in_array($row['id'], $fav_ids);
                if ($row['enable_monitoring'] == 1) {
                    $row['error'] = (int) $row['monitoring_status'] == 1 ? '' : 'link_fault';
                    $row['open'] = (int) $row['monitoring_status'] == 1;
                } else {
                    $row['error'] = '';
                    $row['open'] = 1;
                }
                $row['radio'] = true;
                $row['page'] = \ceil($counter / $delimiter);
                ++$counter;
                return $row;
            }, $this->response['data']);
            if (\array_key_exists('fav', $_REQUEST) && (int) $_REQUEST['fav'] == 1) {
                \reset($this->response['data']);
                while (list($key, $row) = \each($this->response['data'])) {
                    $this->response['data'][$key]['number'] = (string) ($key + 1);
                }
            }
        }
        return $this->response;
    }
    public function getFavIds()
    {
        if ($this->getFav() !== false && !empty($this->fav_radio)) {
            $fav_str = \implode(',', $this->fav_radio);
        } else {
            $fav_str = 'null';
        }
        $fav_ids = $this->db->from('radio')->in('id', $this->fav_radio !== false ? $this->fav_radio : [])->where(['status' => 1])->orderby('field(id,' . $fav_str . ')')->get()->all('id');
        return $fav_ids;
    }
    public function getChannelById()
    {
        $number = @$_REQUEST['number'];
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('radio')->where(['status' => 1, 'number' => $number]);
        $this->setResponseData($result);
        return $this->getResponse('prepareData');
    }
    public function getRawAllUserChannels($uid = null)
    {
        if ($uid) {
            if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tariff_plans', false)) {
                $user = \Ministra\Lib\User::getInstance(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
                $user_channels = $user->getServicesByType('radio');
                if ($user_channels == 'all') {
                    return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('radio')->where(['status' => 1])->orderby('number');
                }
                return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('radio')->where(['status' => 1])->in('id', $user_channels)->orderby('number');
            }
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('radio')->where(['status' => 1])->orderby('number');
    }
    public function getLinksForMonitoring($status = false)
    {
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select("id, name as ch_name, cmd as url, 'stream' as type, monitoring_status as status, " . 'enable_monitoring')->from('radio')->where(['enable_monitoring' => 1]);
        if ($status) {
            $result->where(['monitoring_status' => (int) ($status == 'up')]);
        }
        $monitoring_links = $result->orderby('id')->get()->all();
        $monitoring_links = \array_map(function ($row) {
            if (!empty($row['url']) && \preg_match("/(\\S+:\\/\\/\\S+)/", $row['url'], $match)) {
                $row['url'] = $match[1];
            }
            return $row;
        }, $monitoring_links);
        return $monitoring_links;
    }
}
