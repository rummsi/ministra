<?php

namespace Ministra\Lib\Course;

abstract class BaseCourseProvider implements \Ministra\Lib\Course\CourseGetter, \Ministra\Lib\Course\CourseUpdater
{
    const TABLE_NAME = 'course_cache';
    const TITLE_DATE_FORMAT = 'd.m.Y';
    const MAX_SAVING_DAYS = 10;
    protected $title;
    protected $provider;
    protected $codes = array();
    private $db;
    private $data;
    public function __construct($codes, $db)
    {
        $this->codes = \array_map('trim', \explode(',', $codes));
        $this->db = $db;
        $this->title = \_('Exchange rate on');
    }
    public function getData()
    {
        $rez = $this->db->select(['provider', 'exchange_date', 'code', 'currency', 'nominal', 'value', 'updated'])->from(self::TABLE_NAME)->where('provider', '=', $this->provider)->in('code', $this->codes)->orderBy('updated')->get()->all();
        $data = [];
        $onDate = '0001-00-00';
        foreach ($rez as $item) {
            $data[$item['code']][] = $item;
            $onDate = $item['exchange_date'] < $onDate ? $onDate : $item['exchange_date'];
        }
        $date = \DateTime::createFromFormat('Y-m-d', $onDate);
        return ['title' => $this->title . ' ' . $date->format(self::TITLE_DATE_FORMAT), 'data' => $this->analyse($data)];
    }
    private function analyse(array $data)
    {
        foreach ($data as &$item) {
            \usort($item, function ($a, $b) {
                if ($a['exchange_date'] == $b['exchange_date']) {
                    return 0;
                }
                return $a['exchange_date'] > $b['exchange_date'] ? -1 : 1;
            });
        }
        $data2 = [];
        foreach ($data as $record) {
            $diff = \Ministra\Lib\Course\CourseComparator::Diff($record);
            $trend = \Ministra\Lib\Course\CourseComparator::Trend($diff);
            $data2[] = ['code' => $record[0]['code'], 'currency' => $record[0]['nominal'] . ' ' . $record[0]['currency'], 'value' => $record[0]['value'] + 0, 'diff' => ($trend > 0 ? '&nbsp;' : '') . \sprintf('%.4f', $diff), 'trend' => $trend];
        }
        return $data2;
    }
    public function updateData()
    {
        $this->data = $this->parseData();
        $inserted = $this->saveData();
        $removed = $this->clearOldData();
        return \compact('inserted', 'removed');
    }
    protected abstract function parseData();
    private function saveData()
    {
        $add = ['provider' => $this->provider, 'updated' => \date('Y-m-d H:i:s')];
        foreach ($this->data as $record) {
            $this->db->insert(self::TABLE_NAME, \array_merge($add, $record));
        }
        return \count($this->data);
    }
    private function clearOldData()
    {
        $sql = \sprintf("delete from %s where provider = '%s' and updated<DATE_SUB(NOW(), INTERVAL %d DAY)", self::TABLE_NAME, $this->provider, self::MAX_SAVING_DAYS);
        $count = 0;
        $this->db->query($sql);
        return $count;
    }
}
