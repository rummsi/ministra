<?php

namespace Ministra\Admin\Model;

class StoragesModel extends \Ministra\Admin\Model\BaseMinistraModel
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getLogsTotalRows($where = array(), $like = array())
    {
        $params = ['where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getLogsList($params, true);
    }
    public function getLogsList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('master_log as M_L')->where($param['where']);
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
    public function getListTotalRows($where = array(), $like = array())
    {
        $params = ['where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getListList($params, true);
    }
    public function getListList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('storages as S')->where($param['where']);
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
    public function updateStorageCache($param, $id = array())
    {
        return $this->mysqlInstance->update('storage_cache', $param, $id)->total_rows();
    }
    public function getNoCustomVideo()
    {
        return $this->mysqlInstance->from('video')->where(['protocol!=' => 'custom'])->get()->all('id');
    }
    public function getNoCustomKaraoke()
    {
        return $this->mysqlInstance->from('karaoke')->where(['protocol!=' => 'custom'])->get()->all('id');
    }
    public function updateStorages($param, $id)
    {
        return $this->mysqlInstance->update('storages', $param, ['id' => $id])->total_rows();
    }
    public function insertStorages($param)
    {
        return $this->mysqlInstance->insert('storages', $param)->insert_id();
    }
    public function deleteStorages($id)
    {
        return $this->mysqlInstance->delete('storages', ['id' => $id])->total_rows();
    }
    public function getTotalRowsVideoList($select = array(), $where = array(), $like = array(), $having = array())
    {
        $params = ['select' => ['*', 'GROUP_CONCAT(`storage_name`) as `storages`', 'count(`storage_name`) as `on_storages`'], 'where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        if (!empty($having)) {
            $params['having'] = $having;
        }
        return $this->getVideoList($params, true);
    }
    public function getVideoList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('video, storage_cache')->where($param['where'])->where(['media_type' => 'vclub', 'video.id=storage_cache.media_id  and ' => '1=1', 'storage_cache.status' => '1'])->like($param['like'], 'OR');
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        }
        $this->mysqlInstance->groupby('media_id');
        if (!empty($param['having'])) {
            $this->mysqlInstance->having($param['having']);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        if ($counter) {
            $result = $this->mysqlInstance->get()->all();
            return \count($result);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getRecFilesByStorageName($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('rec_files');
        if (!empty($param['where'])) {
            $this->mysqlInstance->like($param['where']);
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
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
}
