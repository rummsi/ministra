<?php

namespace Ministra\Admin\Service\Statistic;

class LicenseKeysStatistic
{
    const KEYS_TABLE = 'smac_codes';
    const KEY_STANDARD = 'Standard';
    const KEY_ADVANCED = 'Advanced';
    private $db;
    private $data = array(self::KEY_STANDARD => array(), self::KEY_ADVANCED => array());
    public function __construct($db)
    {
        $this->db = $db;
    }
    public function process()
    {
        foreach ($this->getRawData() as $item) {
            $type = $this->getType($item);
            $cnt = \array_key_exists($item['status'], $this->data[$type]) ? $this->data[$type][$item['status']] + $item['cnt'] : $item['cnt'];
            $this->data[$type][$item['status']] = $cnt;
        }
    }
    public function totalStandard($status)
    {
        $standard = $this->data[self::KEY_STANDARD];
        return \array_key_exists($status, $standard) ? $standard[$status] : 0;
    }
    public function totalAdvanced($status)
    {
        $standard = $this->data[self::KEY_ADVANCED];
        return \array_key_exists($status, $standard) ? $standard[$status] : 0;
    }
    private function getRawData()
    {
        $query = $this->db;
        $query->select('count("id") as cnt, `status`, substring(`code`, 2, 1) as code_type')->from(self::KEYS_TABLE)->groupBy('code_type, status')->having(['code_type > 0 and code_type <= ' => 4]);
        return $query->get()->All();
    }
    private function getType($item)
    {
        if (\in_array($item['code_type'], [1, 2])) {
            return self::KEY_STANDARD;
        }
        if (\in_array($item['code_type'], [3, 4])) {
            return self::KEY_ADVANCED;
        }
    }
}
