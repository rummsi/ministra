<?php

namespace Ministra\Admin\Service\Statistic;

class GroupedStatistic
{
    public $table;
    public $groupField = 'status';
    public $absentValue = 0;
    private $db;
    private $data = array();
    public function __construct($db)
    {
        $this->db = $db;
    }
    public function process($table, $field = null)
    {
        $this->table = $table;
        if ($field) {
            $this->groupField = $field;
        }
        $data = $this->getRawData();
        foreach ($data as $item) {
            $grouper = $item[$this->groupField];
            $cnt = \array_key_exists($grouper, $this->data) ? $this->data[$grouper] + $item['cnt'] : $item['cnt'];
            $this->data[$grouper] = $cnt;
        }
    }
    public function countItemsBy($field)
    {
        return isset($this->data[$field]) ? $this->data[$field] : $this->absentValue;
    }
    public function countTotal()
    {
        return \array_sum($this->data);
    }
    private function getRawData()
    {
        $this->db->select('count("id") as cnt, ' . $this->groupField)->from($this->table)->groupBy($this->groupField);
        return $this->db->get()->All();
    }
}
