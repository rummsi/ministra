<?php

namespace Ministra\Lib;

use ErrorException;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class Reseller
{
    const DB_TABLE_NAME = 'reseller';
    private $db;
    private $data = array();
    public function __construct($id = null)
    {
        $fields = $this->getDBInstance()->query('DESCRIBE reseller')->all('Field');
        $this->data = \array_fill_keys($fields, null);
        if (!empty($id)) {
            $this->setData(['id' => $id]);
            $this->updateData();
        }
    }
    private function getDBInstance()
    {
        if (!$this->db instanceof \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89) {
            $this->db = clone \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        }
        return $this->db;
    }
    public function updateData()
    {
        $this->setData($this->getDBData());
    }
    public function getDBData()
    {
        $field = !empty($this->getData('id')) ? 'id' : 'name';
        $result = $this->getDBInstance()->from(self::DB_TABLE_NAME)->where([$field => $this->getData($field)])->get()->first();
        return !empty($result) ? $result : [];
    }
    public function getData($field_name = null)
    {
        return empty($field_name) ? $this->data : (\array_key_exists($field_name, $this->data) ? $this->data[$field_name] : null);
    }
    public function setData($params = array())
    {
        $this->data = \array_replace($this->data, \array_intersect_key($params, $this->data));
    }
    public function updateDBData()
    {
        $result = false;
        if (!empty($this->getDBData())) {
            $result = $this->getDBInstance()->update(self::DB_TABLE_NAME, $this->reduceData(['id', 'modified', 'created']), ['id' => $this->getData('id')])->total_rows();
        } else {
            $data = $this->reduceData(['id', 'modified']);
            $data['created'] = 'NOW()';
            $result = $this->getDBInstance()->insert(self::DB_TABLE_NAME, $data)->insert_id();
        }
        return $result !== false ? $this->getData('id') : false;
    }
    public function reduceData($params = array())
    {
        if (!empty($params)) {
            $remove = \array_intersect_key(\array_flip($params), $this->data);
            return \array_diff_key($this->data, $remove);
        }
        return $this->data;
    }
    public function deleteData()
    {
        $reseller_users = $this->db->count()->from('users')->where(['reseller_id' => $this->getData('id')])->get()->counter();
        if (!empty($reseller_users)) {
            throw new \ErrorException('Finded reseller\'s users, cannot delete reseller, operation canceled');
        }
        return $this->getDBInstance()->delete(self::DB_TABLE_NAME, ['id' => $this->getData('id')])->total_rows();
    }
    public function cleanData()
    {
        $this->data = \array_fill_keys(\array_keys($this->data), null);
    }
}
