<?php

namespace Ministra\Admin\Service\Statistic;

use Ministra\Admin\Interfaces\ActivityType;
class StreamServerStatistic
{
    const KEYS_TABLE = 'users';
    private $db;
    private $offlineTime;
    private $data = array();
    private $reseller;
    public function __construct($db, $reseller = null)
    {
        $this->db = $db;
        $this->reseller = $reseller;
    }
    public function process($time, $ids = array())
    {
        $this->offlineTime = $time;
        foreach ($this->getRawData($ids) as $item) {
            $this->data[$item['now_playing_streamer_id']] = $item['cnt'];
        }
    }
    private function getRawData($ids = array())
    {
        $query = $this->db->select('count("id") as cnt, now_playing_streamer_id')->from(self::KEYS_TABLE);
        if ($ids) {
            $query->in('now_playing_streamer_id', $ids);
        }
        if ($this->reseller) {
            $query->where(['reseller_id' => $this->reseller]);
        }
        $query->where(['now_playing_type' => \Ministra\Admin\Interfaces\ActivityType::ITV, 'UNIX_TIMESTAMP(keep_alive)>' => $this->offlineTime])->groupBy('now_playing_streamer_id');
        return $query->get()->All();
    }
    public function totalSessionsByServer($id)
    {
        return \array_key_exists($id, $this->data) ? $this->data[$id] : 0;
    }
}
