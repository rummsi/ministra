<?php

namespace Ministra\Admin\Service\Statistic;

class VideoStatistic
{
    const KEYS_TABLE = 'video';
    const VIDEO_FILMS = 'Films';
    const VIDEO_SERIALS = 'Serials';
    private $db;
    private $data = array(self::VIDEO_FILMS => array(), self::VIDEO_SERIALS => array());
    public function __construct($db)
    {
        $this->db = $db;
    }
    public function process()
    {
        $data = $this->getRawData();
        foreach ($data as $item) {
            $type = $item['is_series'] == 1 ? self::VIDEO_SERIALS : self::VIDEO_FILMS;
            $cnt = \array_key_exists($item['published'], $this->data[$type]) ? $this->data[$type][$item['published']] + $item['cnt'] : $item['cnt'];
            $this->data[$type][$item['published']] = $cnt;
        }
    }
    public function countFilms($published)
    {
        $films = $this->data[self::VIDEO_FILMS];
        return \array_key_exists($published, $films) ? $films[$published] : 0;
    }
    public function countSerials($published)
    {
        $films = $this->data[self::VIDEO_SERIALS];
        return \array_key_exists($published, $films) ? $films[$published] : 0;
    }
    private function getRawData()
    {
        $this->db->select('count("id") as cnt, if(`status` = 1 and accessed = 1, 1, 0) as `published`, `is_series`')->from(self::KEYS_TABLE)->groupBy('published, is_series');
        return $this->db->get()->All();
    }
    public function getData()
    {
        return $this->data;
    }
    public function countTotalFilms()
    {
        return \array_sum($this->data[self::VIDEO_FILMS]);
    }
    public function countTotalSerials()
    {
        return \array_sum($this->data[self::VIDEO_SERIALS]);
    }
}
