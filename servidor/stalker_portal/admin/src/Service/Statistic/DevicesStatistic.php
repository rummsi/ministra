<?php

namespace Ministra\Admin\Service\Statistic;

class DevicesStatistic
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
    public function process($time)
    {
        $this->offlineTime = $time;
        foreach ($this->getRawData() as $item) {
            $cnt = \array_key_exists($item['online'], $this->data) ? $this->data[$item['online']] + $item['cnt'] : $item['cnt'];
            $this->data[$item['online']] = $cnt;
        }
    }
    private function getRawData()
    {
        $select = \sprintf("count('id') as cnt, if(UNIX_TIMESTAMP(keep_alive) > %d, 'on', 'off') as online", $this->offlineTime);
        $query = $this->db->select($select)->from(self::KEYS_TABLE);
        if ($this->reseller) {
            $query->where(['reseller_id' => $this->reseller]);
        }
        $query->groupBy('online');
        return $query->get()->All();
    }
    public function countOnline()
    {
        return \array_key_exists('on', $this->data) ? $this->data['on'] : 0;
    }
    public function countOffline()
    {
        return \array_key_exists('off', $this->data) ? $this->data['off'] : 0;
    }
}
