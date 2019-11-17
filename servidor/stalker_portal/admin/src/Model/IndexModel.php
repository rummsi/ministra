<?php

namespace Ministra\Admin\Model;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class IndexModel extends \Ministra\Admin\Model\BaseMinistraModel
{
    public function __construct()
    {
        parent::__construct();
    }
    public function deleteDropdownAttribute($param)
    {
        return $this->mysqlInstance->delete('admin_dropdown_attributes', $param)->total_rows();
    }
    public function insertDropdownAttribute($param)
    {
        return $this->mysqlInstance->insert('admin_dropdown_attributes', $param)->insert_id();
    }
    public function get_users($state = 'online', $mobile = false)
    {
        $this->mysqlInstance->from('users')->count()->where(['UNIX_TIMESTAMP(keep_alive)' . ($state == 'online' ? '>' : '<=') => \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2]);
        if ($mobile) {
            $this->mysqlInstance->where(["client_type='Android' OR client_type='Robot' OR client_type=" => 'iOS']);
        }
        if (!empty($this->reseller_id)) {
            $this->mysqlInstance->where(['reseller_id' => $this->reseller_id]);
        }
        return $this->mysqlInstance->get()->counter();
    }
    public function getCountForStatistics($table, $where = array(), $groupby = '')
    {
        $this->mysqlInstance->from($table)->count();
        if (!empty($where)) {
            $this->mysqlInstance->where($where);
        }
        if (!empty($groupby)) {
            $this->mysqlInstance->groupby($groupby);
        }
        return $this->mysqlInstance->get()->counter();
    }
    public function getStorages()
    {
        return $this->mysqlInstance->from('storages')->where(['status' => 1])->get()->all();
    }
    public function getStoragesRecords($storage_name, $total_storage_loading = false)
    {
        $this->mysqlInstance->select(['storage_name', 'now_playing_type', 'count(now_playing_type) as `count`'])->from('users')->where(['UNIX_TIMESTAMP(keep_alive)>' => \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2, 'storage_name' => $storage_name]);
        if (!empty($this->reseller_id) && !$total_storage_loading) {
            $this->mysqlInstance->where(['reseller_id' => $this->reseller_id]);
        }
        if (!$total_storage_loading) {
            $this->mysqlInstance->in('now_playing_type', [2, 11, 14]);
            return $this->mysqlInstance->groupby('now_playing_type')->get()->all();
        }
        return $this->mysqlInstance->groupby('now_playing_type')->get()->first('count');
    }
    public function getStreamServer()
    {
        return $this->mysqlInstance->from('streaming_servers')->where(['status' => 1])->orderby('name')->get()->all();
    }
    public function getStreamingTotal($active = true)
    {
        return $this->mysqlInstance->from('streaming_servers')->count()->where(['status' => $active ? 1 : 0])->get()->counter();
    }
    public function getStreamServerStatus($server_id, $total_server_loading = false)
    {
        $this->mysqlInstance->from('users')->where(['now_playing_streamer_id' => $server_id, 'keep_alive>' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2), 'now_playing_type' => 1]);
        if (!empty($this->reseller_id) && $total_server_loading) {
            $this->mysqlInstance->where(['reseller_id' => $this->reseller_id]);
        }
        return $this->mysqlInstance->count()->get()->counter();
    }
    public function getCurActivePlayingType($type = 100)
    {
        $this->mysqlInstance->from('users')->count()->where(['now_playing_type' => $type, 'keep_alive>' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2)]);
        if (!empty($this->reseller_id)) {
            $this->mysqlInstance->where(['reseller_id' => $this->reseller_id]);
        }
        return $this->mysqlInstance->get()->counter();
    }
    public function getUsersActivity()
    {
        return $this->mysqlInstance->select(['unix_timestamp(`time`) as `time`', 'users_online'])->from('users_activity')->get()->all();
    }
    public function getOpinionFormFlag($flag = null)
    {
        return $this->mysqlInstance->update('administrators', ['opinion_form_flag' => $flag], ['id' => $this->admin_id])->total_rows();
    }
}
