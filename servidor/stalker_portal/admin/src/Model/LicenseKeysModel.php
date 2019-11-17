<?php

namespace Ministra\Admin\Model;

class LicenseKeysModel extends \Ministra\Admin\Model\BaseMinistraModel
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getLicenseKeysRowsList($incoming = array(), $all = false)
    {
        if ($all) {
            $incoming['like'] = $incoming['where'] = [];
        }
        return $this->getLicenseKeysList($incoming, true);
    }
    public function getLicenseKeysList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('smac_codes as S_C');
        if (\array_key_exists('joined', $param)) {
            foreach ($param['joined'] as $table => $keys) {
                $this->mysqlInstance->join($table, $keys['left_key'], $keys['right_key'], $keys['type']);
            }
        }
        if (!empty($param['where'])) {
            $this->mysqlInstance->where($param['where']);
        }
        if (!empty($param['in']) && \is_array($param['in'])) {
            foreach ($param['in'] as $field => $values) {
                $this->mysqlInstance->in($field, \is_array($values) ? $values : [$values]);
            }
        }
        if (!empty($param['like'])) {
            $this->mysqlInstance->like($param['like'], 'OR');
        }
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        }
        if (!empty($param['groupby'])) {
            $this->mysqlInstance->groupby($param['groupby']);
        }
        if (!empty($param['limit']['limit']) && !$counter) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function updateLicenseKeys($params, $where)
    {
        if (!\is_array($where)) {
            $where = ['id' => $where];
        }
        return $this->mysqlInstance->update('smac_codes', $params, $where)->total_rows();
    }
    public function insertSourceData($params)
    {
        return $this->mysqlInstance->insert('ext_adv_sources', $params)->insert_id();
    }
    public function deleteSourceData($id)
    {
        return $this->mysqlInstance->delete('ext_adv_sources', ['id' => $id])->total_rows();
    }
    public function insertCompanyData($params)
    {
        return $this->mysqlInstance->insert('ext_adv_campaigns', $params)->insert_id();
    }
    public function updateCompanyData($params, $id)
    {
        $where = ['id' => $id];
        return $this->mysqlInstance->update('ext_adv_campaigns', $params, $where)->total_rows();
    }
    public function deleteCompanyData($params)
    {
        if (\is_numeric($params)) {
            $params = ['id' => $params];
        }
        return $this->mysqlInstance->delete('ext_adv_campaigns', $params)->total_rows();
    }
    public function getCompanyRowsList($incoming = array(), $all = false)
    {
        if ($all) {
            $incoming['like'] = [];
        }
        return $this->getCompanyList($incoming, true);
    }
    public function getCompanyList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('ext_adv_campaigns as E_A_C');
        if (\array_key_exists('joined', $param)) {
            foreach ($param['joined'] as $table => $keys) {
                $this->mysqlInstance->join($table, $keys['left_key'], $keys['right_key'], $keys['type']);
            }
        }
        if (!empty($param['where'])) {
            $this->mysqlInstance->where($param['where']);
        }
        if (!empty($param['like'])) {
            $this->mysqlInstance->like($param['like'], 'OR');
        }
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        }
        if (!empty($param['groupby'])) {
            $this->mysqlInstance->groupby($param['groupby']);
        }
        if ($counter) {
            $result = $this->mysqlInstance->count()->get()->first();
            return \is_array($result) ? \array_sum($result) : $result;
        }
        if (!empty($param['limit']['limit']) && !$counter) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getAdPositions($id)
    {
        return $this->mysqlInstance->select()->from('ext_adv_campaigns_position')->where(['campaigns_id' => $id])->get()->all();
    }
    public function delAdPositions($id, $positions)
    {
        return $this->mysqlInstance->delete('ext_adv_campaigns_position', ['campaigns_id' => $id, 'position_code in (' . \implode(', ', $positions) . ') and 1' => 1])->total_rows();
    }
    public function addAdPositions($id, $positions = array(), $skip = array())
    {
        $insert = [];
        \reset($positions);
        while (list($key, $val) = \each($positions)) {
            $insert[] = ['campaigns_id' => $id, 'position_code' => $key, 'blocks' => $val, 'skip_after' => !empty($skip[$key]) ? (int) $skip[$key] : 0];
        }
        return $this->mysqlInstance->insert('ext_adv_campaigns_position', \array_values($insert))->total_rows();
    }
}
