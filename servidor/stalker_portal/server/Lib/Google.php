<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class Google
{
    public $gapi_name;
    public $gapi_url;
    public $cache_expire = 3600;
    public $gapi_module;
    public $gapi_field;
    private $db;
    private $gapi_arr;
    private $cache_table;
    public function __construct()
    {
        $this->db = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        $this->cache_table = 'gapi_cache_' . $this->gapi_name;
    }
    public function getData()
    {
        return $this->getDataFromDBCache();
    }
    private function getDataFromDBCache()
    {
        $content = $this->db->from($this->cache_table)->get()->first('content');
        $content = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\base64_decode($content));
        if (\is_array($content)) {
            return $content;
        }
        return 0;
    }
    public function getDataFromGAPI()
    {
        $gapi_arr = [];
        $gapi_resp = \simplexml_load_file($this->gapi_url);
        if ($gapi_resp) {
            foreach ($gapi_resp->{$this->gapi_module}->{$this->gapi_field} as $item) {
                $new_item = [];
                foreach ($item as $field => $data) {
                    $new_item[$field] = (string) $data->attributes()->data;
                }
                $gapi_arr[] = $new_item;
            }
            $this->setDataDBCache($gapi_arr);
            return $gapi_arr;
        }
    }
    private function setDataDBCache($arr)
    {
        $content = \base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($arr));
        $result = $this->db->from($this->cache_table)->get();
        $crc = $result->get('crc');
        if (\md5($content) != $crc) {
            $data = ['content' => $content, 'updated' => 'NOW()', 'url' => $this->gapi_url, 'crc' => \md5($content)];
            if ($result->count() == 1) {
                $this->db->update($this->cache_table, $data);
            } else {
                $this->db->insert($this->cache_table, $data);
            }
        } else {
            if ($result->count() == 1) {
                $this->db->update($this->cache_table, ['updated' => 'NOW()']);
            }
        }
    }
}
