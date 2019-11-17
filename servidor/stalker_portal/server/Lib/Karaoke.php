<?php

namespace Ministra\Lib;

use Exception;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class Karaoke extends \Ministra\Lib\AjaxResponse implements \Ministra\Lib\StbApi\Karaoke
{
    private static $instance = null;
    public $fav_karaoke = false;
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public static function getById($id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('karaoke')->where(['id' => (int) $id])->get()->first();
    }
    public function createLink()
    {
        \preg_match("/\\/media\\/(\\d+).mpg\$/", $_REQUEST['cmd'], $tmp_arr);
        $media_id = $tmp_arr[1];
        $res = $this->getLinkByKaraokeId($media_id);
        \var_dump($res);
        return $res;
    }
    public function getLinkByKaraokeId($karaoke_id)
    {
        $master = new \Ministra\Lib\KaraokeMaster();
        try {
            $res = $master->play($karaoke_id);
        } catch (\Exception $e) {
            \trigger_error($e->getMessage());
        }
        return $res;
    }
    public function getOrderedList()
    {
        if ($this->getFav($this->stb->id) !== false) {
            $fav_str = \implode(',', $this->fav_karaoke);
        } else {
            $fav_str = 'null';
        }
        $result = $this->getData();
        if (@$_REQUEST['sortby']) {
            $sortby = $_REQUEST['sortby'];
            if ($sortby == 'name') {
                $result = $result->orderby('karaoke.name');
            } elseif ($sortby == 'singer') {
                $result = $result->orderby('karaoke.singer');
            } elseif ($sortby == 'fav') {
                $result = $result->orderby('field(id,' . $fav_str . ')');
            }
        } else {
            $result = $result->orderby('karaoke.singer');
        }
        if (@$_REQUEST['fav']) {
            $result = $result->in('karaoke.id', $this->fav_karaoke !== false ? $this->fav_karaoke : []);
        }
        $this->setResponseData($result);
        return $this->getResponse('prepareData');
    }
    public function getFav($uid = null)
    {
        if (!$uid) {
            $uid = $this->stb->id;
        }
        if ($this->fav_karaoke === false) {
            $fav_karaoke_ids_arr = $this->db->select('fav_karaoke')->from('fav_karaoke')->where(['uid' => (int) $uid])->use_caching(['fav_karaoke.uid=' . (int) $uid])->get()->first('fav_karaoke');
            if (!empty($fav_karaoke_ids_arr)) {
                $this->fav_karaoke = \is_string($fav_karaoke_ids_arr) ? \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac($fav_karaoke_ids_arr) : false;
            }
        }
        return $this->fav_karaoke;
    }
    private function getData()
    {
        $offset = $this->page * self::MAX_PAGE_ITEMS;
        $where = ['status' => 1];
        if (!$this->stb->c6e0d92fc0ec62469764ba74feb893fa()) {
            $where['accessed'] = 1;
        }
        $like = [];
        if (@$_REQUEST['abc'] && @$_REQUEST['abc'] !== '*') {
            $letter = $_REQUEST['abc'];
            if (@$_REQUEST['sortby'] == 'name') {
                $like = ['karaoke.name' => $letter . '%'];
            } else {
                $like = ['karaoke.singer' => $letter . '%'];
            }
        }
        if (@$_REQUEST['search']) {
            $letters = $_REQUEST['search'];
            $search['karaoke.name'] = '%' . $letters . '%';
            $search['karaoke.singer'] = '%' . $letters . '%';
        }
        return $this->db->from('karaoke')->where($where)->like($like)->like($search, 'OR ')->limit(self::MAX_PAGE_ITEMS, $offset);
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
        return $this->setClaimGlobal('karaoke');
    }
    public function setFav($uid = null)
    {
        if (!$uid) {
            $uid = $this->stb->id;
        }
        $fav_karaoke = @$_REQUEST['fav_karaoke'];
        if (empty($fav_karaoke)) {
            $fav_karaoke = [];
        } else {
            $fav_karaoke = \explode(',', $fav_karaoke);
        }
        if (\is_array($fav_karaoke)) {
            return $this->saveFav(\array_unique($fav_karaoke), $uid);
        }
        return true;
    }
    public function saveFav(array $fav_array, $uid)
    {
        if (empty($uid)) {
            return false;
        }
        $fav_ch_str = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($fav_array);
        if (empty($this->fav_karaoke)) {
            $this->getFav($uid);
        }
        if ($this->fav_karaoke === false) {
            return $this->db->use_caching(['fav_karaoke.uid=' . (int) $uid])->insert('fav_karaoke', ['uid' => (int) $uid, 'fav_karaoke' => $fav_ch_str, 'addtime' => 'NOW()'])->insert_id();
        }
        return $this->db->use_caching(['fav_karaoke.uid=' . (int) $uid])->update('fav_karaoke', ['fav_karaoke' => $fav_ch_str, 'edittime' => 'NOW()'], ['uid' => (int) $uid])->result();
    }
    public function getAllFavKaraoke()
    {
        if ($this->getFav() !== false && !empty($this->fav_karaoke)) {
            $fav_str = \implode(',', $this->fav_karaoke);
        } else {
            $fav_str = 'null';
        }
        $fav_karaoke = $this->db->from('karaoke')->in('id', $this->fav_karaoke !== false ? $this->fav_karaoke : [])->where(['status' => 1])->orderby('field(id,' . $fav_str . ')');
        $this->setResponseData($fav_karaoke);
        return $this->getResponse('prepareData');
    }
    public function getUrlByKaraokeId($karaoke_id)
    {
        $link = $this->getLinkByKaraokeId($karaoke_id);
        if (empty($link['cmd'])) {
            throw new \Exception('Obtaining url failed');
        }
        if (!empty($link['storage_id'])) {
            $storage = \Ministra\Lib\Master::getStorageById($link['storage_id']);
            if (!empty($storage)) {
                $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
                $cache->set($this->stb->id . '_playback', ['type' => 'karaoke', 'id' => $link['id'], 'storage' => $storage['storage_name'], 'storage_id' => $storage['id']], 0, 10);
            }
        } else {
            $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
            $cache->del($this->stb->id . '_playback');
        }
        return $link['cmd'];
    }
    public function prepareData()
    {
        $fav_ids = $this->getFavIds();
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $this->response['data'][$i]['fav'] = (int) \in_array($this->response['data'][$i]['id'], $fav_ids);
            if (empty($this->response['data'][$i]['rtsp_url'])) {
                $this->response['data'][$i]['cmd'] = '/media/' . $this->response['data'][$i]['id'] . '.mpg';
            } else {
                $this->response['data'][$i]['cmd'] = $this->response['data'][$i]['rtsp_url'];
            }
        }
        return $this->response;
    }
    public function getFavIds()
    {
        if ($this->getFav() !== false && !empty($this->fav_karaoke)) {
            $fav_str = \implode(',', $this->fav_karaoke);
        } else {
            $fav_str = 'null';
        }
        $fav_ids = $this->db->from('karaoke')->in('id', $this->fav_karaoke !== false ? $this->fav_karaoke : [])->where(['status' => 1])->orderby('field(id,' . $fav_str . ')')->get()->all('id');
        return $fav_ids;
    }
    public function setFavStatus()
    {
    }
    public function getRawAll()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('karaoke.*, karaoke_genre.title as genre')->from('karaoke')->join('karaoke_genre', 'karaoke.genre_id', 'karaoke_genre.id', 'LEFT')->where(['accessed' => 1])->where(['status' => 1, 'protocol' => 'custom'], ' OR ');
    }
}
