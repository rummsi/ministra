<?php

namespace Ministra\Lib;

use ErrorException;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\a094d1edcf31fb42e4aeffbc078b6297;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class Storage
{
    private $storage;
    private $max_failures;
    private $stat_period;
    public function __construct($init_info = array())
    {
        if (empty($init_info)) {
            return;
        }
        if (!empty($init_info['id'])) {
            $this->storage = $this->getById($init_info['id']);
        } else {
            if (!empty($init_info['name'])) {
                $this->storage = $this->getByName($init_info['name']);
            }
        }
        if (empty($this->storage)) {
            throw new \ErrorException('Storage can not be initialized with values: ' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\a094d1edcf31fb42e4aeffbc078b6297::t507c29090b5ee6c8967c58b7903a8965($init_info));
        }
        $this->max_failures = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('max_storage_failures', 3);
        $this->stat_period = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('storage_stat_period', 300);
    }
    public function getById($id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('storages')->where(['id' => $id])->get()->first();
    }
    public function getByName($name)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('storages')->where(['storage_name' => $name])->get()->first();
    }
    public function markAsFailed($description = '')
    {
        $this->checkIfInitialized();
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('storages_failure', ['storage_id' => $this->storage['id'], 'description' => $description])->insert_id();
        $failures = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('storages_failure')->count()->where(['storage_id' => $this->storage['id'], 'added>' => \date('Y-m-d H:i:s', \time() - $this->stat_period)])->get()->counter();
        if ($failures >= $this->max_failures) {
            $this->setOff();
        }
    }
    private function checkIfInitialized()
    {
        if (empty($this->storage)) {
            throw new \ErrorException('Storage not initialized');
        }
        return true;
    }
    public function setOff()
    {
        $this->checkIfInitialized();
        if ($this->storage['status'] == 0) {
            return true;
        }
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('storages', ['status' => 0], ['id' => $this->storage['id']]);
        if ($result) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('master_log', ['log_txt' => 'Storage ' . $this->storage['storage_name'] . ' has been disabled after ' . $this->max_failures . ' failures in ' . $this->stat_period . 's', 'added' => 'NOW()']);
        }
        return $result;
    }
}
