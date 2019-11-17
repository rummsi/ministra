<?php

namespace Ministra\Admin\Service\Statistic;

use Ministra\Admin\Interfaces\ActivityType;
class StorageServerStatistic
{
    const KEYS_TABLE = 'users';
    private $db;
    private $offlineTime;
    private $data = array();
    private $storageActivityTypes = array(\Ministra\Admin\Interfaces\ActivityType::VIDEO, \Ministra\Admin\Interfaces\ActivityType::TV_ARCHIVE, \Ministra\Admin\Interfaces\ActivityType::TIMESHIFT);
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
            $name = $item['storage_name'];
            if (!$this->isStorageActivity($item['now_playing_type'])) {
                continue;
            }
            if (!\array_key_exists($name, $this->data)) {
                $this->data[$name] = ['total' => 0];
            }
            $this->data[$name]['total'] += $item['cnt'];
            if ($this->reseller && $this->reseller != $item['reseller_id']) {
                continue;
            }
            if (\array_key_exists($item['now_playing_type'], $this->data[$name])) {
                $this->data[$name][$item['now_playing_type']] += $item['cnt'];
            } else {
                $this->data[$name][$item['now_playing_type']] = (int) $item['cnt'];
            }
        }
    }
    private function getRawData($names = array())
    {
        $query = $this->db->select('count("id") as cnt, storage_name,now_playing_type,reseller_id')->from(self::KEYS_TABLE);
        if ($names) {
            $query->in('storage_name', $names);
        }
        $query->where(['UNIX_TIMESTAMP(keep_alive)>' => $this->offlineTime])->groupBy('storage_name,now_playing_type,reseller_id');
        return $query->get()->All();
    }
    public function totalSessionsByServer($storage_name)
    {
        return \array_key_exists($storage_name, $this->data) ? $this->data[$storage_name] : [];
    }
    private function isStorageActivity($type)
    {
        return \in_array($type, $this->storageActivityTypes);
    }
}
