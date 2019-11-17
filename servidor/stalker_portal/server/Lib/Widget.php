<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class Widget
{
    public $db;
    public $widget_name;
    public $cache_table;
    public $cache_expire = 3600;
    public $rss_url;
    public $rss_fields = array();
    public $rss_atributes = array();
    public function __construct()
    {
        $this->db = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        $this->cache_table = 'rss_cache_' . $this->widget_name;
    }
    public function getData()
    {
        return $this->getDataFromDBCache();
    }
    private function getDataFromDBCache()
    {
        $content = $this->db->get($this->cache_table)->first('content');
        $content = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\base64_decode($content));
        if (\is_array($content)) {
            return $content;
        }
        return 0;
    }
    public function getDataFromRSS()
    {
        $rss_new = [];
        $rss = \simplexml_load_file($this->rss_url);
        if ($rss) {
            foreach ($rss->channel->item as $item) {
                $new_item = [];
                foreach ($this->rss_fields as $field) {
                    $new_item[$field] = (string) $item->{$field};
                }
                foreach ($this->rss_atributes as $atribute) {
                    $new_item['attributes_' . $atribute] = (string) $item->enclosure->attributes()->{$atribute};
                }
                $rss_new[] = $new_item;
            }
            $this->setDataDBCache($rss_new);
            return $rss_new;
        }
    }
    private function setDataDBCache($arr)
    {
        $content = \base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($arr));
        $result = $this->db->get($this->cache_table);
        if (\md5($content) != $result->first('crc')) {
            $data = ['content' => $content, 'updated' => 'NOW()', 'url' => $this->rss_url, 'crc' => \md5($content)];
            if ($result->count() == 1) {
                $this->db->update($this->cache_table, $data);
            } else {
                $this->db->insert($this->cache_table, $data);
            }
        }
    }
}
