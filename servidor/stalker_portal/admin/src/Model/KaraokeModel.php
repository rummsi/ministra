<?php

namespace Ministra\Admin\Model;

class KaraokeModel extends \Ministra\Admin\Model\BaseMinistraModel
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getTotalRowsKaraokeList($where = array(), $like = array())
    {
        $params = ['where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getKaraokeList($params, true);
    }
    public function getKaraokeList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('karaoke')->join('administrators', 'administrators.id', 'karaoke.add_by', 'LEFT')->join('media_claims', 'karaoke.id', 'media_claims.media_id', 'LEFT')->where($param['where']);
        if (!empty($param['like'])) {
            $this->mysqlInstance->like($param['like'], 'OR');
        }
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        }
        if (!$counter) {
            $this->mysqlInstance->groupby(['karaoke.id', 'karaoke.add_by']);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function updateKaraoke($param, $where)
    {
        $where = \is_array($where) ? $where : ['id' => $where];
        return $this->mysqlInstance->update('karaoke', $param, $where)->total_rows();
    }
    public function insertKaraoke($param)
    {
        return $this->mysqlInstance->insert('karaoke', $param)->insert_id();
    }
    public function deleteKaraoke($param)
    {
        return $this->mysqlInstance->delete('karaoke', $param)->total_rows();
    }
}
