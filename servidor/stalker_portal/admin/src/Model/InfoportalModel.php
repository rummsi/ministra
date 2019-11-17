<?php

namespace Ministra\Admin\Model;

class InfoportalModel extends \Ministra\Admin\Model\BaseMinistraModel
{
    public function getTotalRowsPhoneBoockList($table_prefix, $where = array(), $like = array())
    {
        $params = ['where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getPhoneBoockList($table_prefix, $params, true);
    }
    public function getPhoneBoockList($table_prefix, $param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from("{$table_prefix}_city_info");
        if (!empty($param['where'])) {
            $this->mysqlInstance->where($param['where']);
        }
        if (!empty($param['like'])) {
            $this->mysqlInstance->like($param['like'], 'OR');
        }
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function updatePhoneBoock($table_prefix, $param)
    {
        $where = ['id' => $param['id']];
        return $this->mysqlInstance->update("{$table_prefix}_city_info", $param[0], $where)->total_rows();
    }
    public function insertPhoneBoock($table_prefix, $param)
    {
        return $this->mysqlInstance->insert("{$table_prefix}_city_info", $param)->insert_id();
    }
    public function deletePhoneBoock($table_prefix, $param)
    {
        return $this->mysqlInstance->delete("{$table_prefix}_city_info", $param)->total_rows();
    }
    public function getTotalRowsHumorList($where = array(), $like = array())
    {
        $params = ['where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getHumorList($params, true);
    }
    public function getHumorList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('anec');
        if (!empty($param['where'])) {
            $this->mysqlInstance->where($param['where']);
        }
        if (!empty($param['like'])) {
            $this->mysqlInstance->like($param['like'], 'OR');
        }
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function updateHumor($param, $where)
    {
        $where = \is_array($where) ? $where : ['id' => $where];
        return $this->mysqlInstance->update('anec', $param, $where)->total_rows();
    }
    public function insertHumor($param)
    {
        return $this->mysqlInstance->insert('anec', $param)->insert_id();
    }
    public function deleteHumor($param)
    {
        return $this->mysqlInstance->delete('anec', $param)->total_rows();
    }
    public function getFirstFreeNumber($table_prefix, $field = 'number', $offset = 1, $direction = 1)
    {
        $min = (int) $this->mysqlInstance->query("SELECT min(`{$table_prefix}_city_info`.`num`) as `empty_number` FROM `{$table_prefix}_city_info`")->first('empty_number');
        if ($min > 1) {
            return 1;
        }
        return $this->mysqlInstance->query("SELECT (`{$table_prefix}_city_info`.`num`+1) as `empty_number`\n                    FROM `{$table_prefix}_city_info`\n                    WHERE (\n                        SELECT 1 FROM `{$table_prefix}_city_info` as `st` WHERE `st`.`num` = (`{$table_prefix}_city_info`.`num` + 1)\n                    ) IS NULL\n                    ORDER BY `{$table_prefix}_city_info`.`num`\n                    LIMIT 1")->first('empty_number');
    }
}
