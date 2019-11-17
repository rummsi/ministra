<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class NimbleTvArchive extends \Ministra\Lib\TvArchive
{
    public function __construct()
    {
        parent::__construct();
    }
    protected function getAllActiveStorages()
    {
        $storages = [];
        $data = $this->db->from('storages')->where(['status' => 1, 'for_records' => 1, 'nimble_dvr' => 1])->get()->all();
        foreach ($data as $idx => $storage) {
            $storages[$storage['storage_name']] = $storage;
            $storages[$storage['storage_name']]['load'] = $this->getStorageLoad($storage);
            $storages[$storage['storage_name']]['storage_ip'] = $storage['storage_ip'];
        }
        $storages = $this->sortByLoad($storages);
        return $storages;
    }
    protected function deleteTaskById($task_id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('tv_archive', ['id' => $task_id]);
    }
}
