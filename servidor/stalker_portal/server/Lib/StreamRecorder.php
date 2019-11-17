<?php

namespace Ministra\Lib;

use Exception;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class StreamRecorder extends \Ministra\Lib\Master
{
    public function __construct()
    {
        $this->media_type = 'remote_pvr';
        $this->db_table = 'users_rec';
        parent::__construct();
    }
    public function startDeferred($program_id, $on_stb = false, $program = null)
    {
        if ($program && empty($program_id)) {
            $epg = $program;
        } else {
            $epg = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('*, UNIX_TIMESTAMP(time) as start_ts, UNIX_TIMESTAMP(time_to) as stop_ts')->from('epg')->where(['real_id' => $program_id])->get()->first();
        }
        $channel = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['id' => (int) $epg['ch_id']])->get()->first();
        $user_rec_id = $this->createUserRecord($channel, false, $epg['time'], $on_stb, $program);
        if (!$user_rec_id) {
            return false;
        }
        return $user_rec_id;
    }
    private function createUserRecord($channel, $auto_start = true, $start_time = 0, $on_stb = false, $virtual_program = null)
    {
        if (!$on_stb) {
            $rest_length = $this->checkTotalUserRecordsLength($this->stb->id);
            if ($rest_length <= 0) {
                throw new \Ministra\Lib\NPVRTotalLengthLimitError();
            }
        } else {
            $rest_length = 0;
        }
        \preg_match("/vtrack:(\\d+)/", $channel['mc_cmd'], $vtrack_arr);
        \preg_match("/atrack:(\\d+)/", $channel['mc_cmd'], $atrack_arr);
        $vtrack = '';
        $atrack = '';
        if (\count($vtrack_arr) > 0) {
            $vtrack = (int) $vtrack_arr[1];
        }
        if (\count($atrack_arr)) {
            $atrack = (int) $atrack_arr[1];
        }
        $data = ['ch_id' => $channel['id'], 'uid' => $this->stb->id, 'atrack' => $atrack, 'vtrack' => $vtrack];
        $epg = new \Ministra\Lib\Epg();
        if ($auto_start) {
            if ($rest_length / 60 - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('record_max_length') < 0) {
                $length = $rest_length;
            } else {
                $length = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('record_max_length') * 60;
            }
            if ($length < 0) {
                throw new \Ministra\Lib\NPVRTotalLengthLimitError();
            }
            $program = $epg->getCurProgram($channel['id']);
            $data['program'] = $program['name'];
            $data['t_start'] = 'NOW()';
            $data['started'] = 1;
            $data['local'] = (int) $on_stb;
            $data['length'] = $length;
            $data['t_stop'] = \date('Y-m-d H:i:s', \time() + $length);
        } else {
            $program = $epg->getProgramByChannelAndTime($channel['id'], $start_time);
            if ($virtual_program && \is_array($virtual_program) && !\array_key_exists('id', $virtual_program)) {
                $program['time'] = $virtual_program['time'];
                $program['time_to'] = $virtual_program['time_to'];
                $start_time = $program['time'];
            } elseif ($virtual_program) {
                $virtual_program['name'] = $program['name'];
                $program = $virtual_program;
            }
            $length = \strtotime($program['time_to']) - \strtotime($program['time']);
            if ($length < 0) {
                throw new \Ministra\Lib\NPVRServerError();
            }
            if (!$on_stb && $rest_length - $length <= 0) {
                throw new \Ministra\Lib\NPVRTotalLengthLimitError();
            }
            $data['program'] = $program['name'];
            $data['program_id'] = $program['id'];
            $data['program_real_id'] = $program['real_id'];
            $data['t_start'] = $start_time;
            $data['length'] = $length;
            $data['t_stop'] = \date('Y-m-d H:i:s', \strtotime($start_time) + $length);
            $data['local'] = (int) $on_stb;
        }
        $user_rec_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('users_rec', $data)->insert_id();
        if ($on_stb) {
            return $user_rec_id;
        }
        return $this->createFileRecord($user_rec_id);
    }
    public function checkTotalUserRecordsLength($uid)
    {
        $length = (int) (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('total_records_length') * 60 - $this->getTotalUserRecordsLength($uid));
        if ($length < 0) {
            $length = 0;
        }
        return $length;
    }
    private function getTotalUserRecordsLength($uid)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('SUM(length) as total_length')->from('users_rec')->where(['uid' => $uid, 'local' => 0])->get()->first('total_length');
    }
    private function createFileRecord($user_rec_id)
    {
        $user_rec = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users_rec')->where(['id' => $user_rec_id])->get()->first();
        if (empty($user_rec)) {
            throw new \Ministra\Lib\NPVRServerError();
        }
        $channel = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['id' => $user_rec['ch_id']])->get()->first();
        if (empty($channel)) {
            throw new \Ministra\Lib\NPVRChannelNotFoundError();
        }
        $rec_file_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('rec_files', ['ch_id' => $user_rec['ch_id'], 't_start' => 'NOW()', 'atrack' => $user_rec['atrack'], 'vtrack' => $user_rec['vtrack']])->insert_id();
        $allowed_storages = \array_keys(\Ministra\Lib\RemotePvr::getStoragesForChannel($user_rec['ch_id']));
        foreach ($this->storages as $name => $storage) {
            if (!\in_array($name, $allowed_storages)) {
                continue;
            }
            if ($storage['load'] < 1) {
                try {
                    $file_name = $this->clients[$name]->resource('recorder')->create(['url' => $channel['mc_cmd'], 'rec_id' => $rec_file_id, 'start_delay' => \strtotime($user_rec['t_start']) - \time(), 'duration' => $user_rec['length']]);
                } catch (\Exception $exception) {
                    try {
                        $this->clients[$name]->resource('recorder')->ids($rec_file_id)->update();
                    } catch (\Exception $exception) {
                        $this->parseException($exception);
                    }
                    $this->deleteUserRecord($user_rec_id);
                    $this->deleteFileRecord($rec_file_id);
                    $this->parseException($exception);
                }
            }
            if (!empty($file_name)) {
                break;
            }
        }
        if (empty($file_name)) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users_rec', ['ended' => 1], ['id' => $user_rec_id]);
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('rec_files', ['ended' => 1], ['id' => $rec_file_id]);
            throw new \Ministra\Lib\NPVRServerError();
        }
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('rec_files', ['storage_name' => $name, 'file_name' => $file_name], ['id' => $rec_file_id]);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users_rec', ['file_id' => $rec_file_id, 'started' => \strtotime($user_rec['t_start']) - \time() > 0 ? 0 : 1], ['id' => $user_rec_id]);
        return $user_rec_id;
    }
    private function deleteUserRecord($user_rec_id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('users_rec', ['id' => $user_rec_id]);
    }
    private function deleteFileRecord($rec_file_id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('rec_files', ['id' => $rec_file_id]);
    }
    public function setStarted($file_id)
    {
        $user_record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users_rec')->where(['file_id' => $file_id])->get()->first();
        if (empty($user_record) || $user_record['ended']) {
            return false;
        }
        $data = ['t_start' => 'NOW()', 'started' => 1];
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users_rec', $data, ['file_id' => $file_id]);
        $channel = \Ministra\Lib\Itv::getChannelById($user_record['ch_id']);
        if (!empty($user_record['program_real_id'])) {
            $event = new \Ministra\Lib\SysEvent();
            $event->setUserListById($user_record['uid']);
            $event->setAutoHideTimeout(300);
            $event->setTtl(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2);
            \Ministra\Lib\User::clear();
            $user = \Ministra\Lib\User::getInstance((int) $user_record['uid']);
            $event->sendMsg($user->getLocalizedText('Starting recording') . ' — ' . $user_record['program'] . ' ' . $user->getLocalizedText('on channel') . ' ' . $channel['name']);
        }
        return true;
    }
    public function setEnded($file_id)
    {
        $user_rec_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users_rec')->where(['file_id' => $file_id])->get()->first('id');
        if (empty($user_rec_id)) {
            return false;
        }
        $stopped = $this->stop($user_rec_id, false);
        if ($stopped) {
            $user_record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users_rec')->where(['id' => $user_rec_id])->get()->first();
            $channel = \Ministra\Lib\Itv::getChannelById($user_record['ch_id']);
            $event = new \Ministra\Lib\SysEvent();
            $event->setUserListById($user_record['uid']);
            $event->setTtl(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2);
            \Ministra\Lib\User::clear();
            $user = \Ministra\Lib\User::getInstance((int) $user_record['uid']);
            $event->sendMsg($user->getLocalizedText('Stopped recording') . ' — ' . $user_record['program'] . ' ' . $user->getLocalizedText('on channel') . ' ' . $channel['name']);
        }
        return $stopped;
    }
    public function stop($user_rec_id, $send_to_storage = true)
    {
        $user_record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users_rec')->where(['id' => $user_rec_id])->get()->first();
        $file_record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('rec_files')->where(['id' => $user_record['file_id']])->get()->first();
        if (\strtotime($user_record['t_start']) > \time()) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('rec_files', ['id' => $file_record['id']]);
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('users_rec', ['id' => $user_record['id']]);
            if ($send_to_storage) {
                try {
                    return $this->clients[$file_record['storage_name']]->resource('recorder')->ids($file_record['id'])->update();
                } catch (\Exception $exception) {
                    $this->parseException($exception);
                    return false;
                }
            }
        } else {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users_rec', ['t_stop' => 'NOW()', 'ended' => 1, 'length' => \time() - \strtotime($user_record['t_start'])], ['id' => $user_rec_id]);
            if ($user_record['started']) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('rec_files', ['t_stop' => 'NOW()', 'ended' => 1, 'length' => \time() - \strtotime($file_record['t_start'])], ['id' => $file_record['id']]);
                if ($send_to_storage) {
                    try {
                        return $this->clients[$file_record['storage_name']]->resource('recorder')->ids($file_record['id'])->update();
                    } catch (\Exception $exception) {
                        $this->parseException($exception);
                        return false;
                    }
                }
            }
        }
        return true;
    }
    public function startDeferredNow($user_rec_id)
    {
        $user_rec_id = (int) $user_rec_id;
        $user_record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users_rec')->where(['id' => $user_rec_id])->get()->first();
        if ($user_record['ended']) {
            $this->deleteUserRecord($user_rec_id);
            return false;
        }
        $data = ['t_start' => 'NOW()', 'started' => 1];
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users_rec', $data, ['id' => $user_rec_id]);
        $file_record = $this->createFileRecord($user_rec_id);
        if ($file_record) {
            $channel = \Ministra\Lib\Itv::getChannelById($user_record['ch_id']);
            $event = new \Ministra\Lib\SysEvent();
            $event->setUserListById($user_record['uid']);
            $event->setAutoHideTimeout(300);
            $event->setTtl(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2);
            $user = \Ministra\Lib\User::getInstance((int) $user_record['uid']);
            $event->sendMsg($user->getLocalizedText('Starting recording') . ' — ' . $user_record['program'] . ' ' . $user->getLocalizedText('on channel') . ' ' . $channel['name']);
        }
        return $file_record;
    }
    public function startNow($channel, $on_stb = false)
    {
        if (!$on_stb) {
            $is_recording = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users_rec')->where(['ch_id' => $channel['id'], 'uid' => $this->stb->id, 'ended' => 0, 'started' => 1])->get()->first();
            if (!empty($is_recording)) {
                throw new \Ministra\Lib\NPVRRecordingAlreadyExistError();
            }
        }
        return $this->createUserRecord($channel, true, 0, $on_stb);
    }
    public function stopDeferred($user_rec_id, $duration_minutes)
    {
        $user_record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('*, UNIX_TIMESTAMP(t_start) as start_ts')->from('users_rec')->where(['id' => $user_rec_id])->get()->first();
        $file_record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('rec_files')->where(['id' => $user_record['file_id']])->get()->first();
        if (!empty($duration_minutes) && $duration_minutes > 0) {
            $rest_length = $this->checkTotalUserRecordsLength($this->stb->id);
            $rest_length += $user_record['length'];
            if ($rest_length / 60 - $duration_minutes <= 0) {
                $duration_minutes = $rest_length / 60;
            }
            $stop_time = (int) ($user_record['start_ts'] + $duration_minutes * 60);
            try {
                $this->clients[$file_record['storage_name']]->resource('recorder')->ids($file_record['id'])->update(['stop_time' => $stop_time]);
            } catch (\Exception $exception) {
                $this->parseException($exception);
            }
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users_rec', ['t_stop' => \date('Y-m-d H:i:s', $stop_time), 'length' => $duration_minutes * 60], ['id' => $user_rec_id]);
            return $stop_time;
        }
        try {
            $this->clients[$file_record['storage_name']]->resource('recorder')->ids($file_record['id'])->update();
        } catch (\Exception $exception) {
            $this->parseException($exception);
        }
        return true;
    }
    public function stopAndUsrMsg($user_rec_id)
    {
        $stopped = $this->stop($user_rec_id);
        if ($stopped) {
            $user_record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users_rec')->where(['id' => $user_rec_id])->get()->first();
            $channel = \Ministra\Lib\Itv::getChannelById($user_record['ch_id']);
            $event = new \Ministra\Lib\SysEvent();
            $event->setUserListById($user_record['uid']);
            $event->setTtl(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2);
            $user = \Ministra\Lib\User::getInstance((int) $user_record['uid']);
            $event->sendMsg($user->getLocalizedText('Stopped recording') . ' — ' . $user_record['program'] . ' ' . $user->getLocalizedText('on channel') . ' ' . $channel['name']);
        }
        return $stopped;
    }
    public function checkStatus($channel)
    {
    }
    public function del($rec_id)
    {
        $user_record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users_rec')->where(['id' => $rec_id])->get()->first();
        $this->deleteUserRecord($rec_id);
        $related_records = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users_rec')->where(['file_id' => $user_record['file_id']])->get()->all();
        if (!empty($related_records)) {
            return true;
        }
        try {
            if (empty($user_record['file_id'])) {
                return true;
            }
            $rec_file = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('rec_files')->where(['id' => $user_record['file_id']])->get()->first();
            if (!empty($rec_file)) {
                return $this->clients[$rec_file['storage_name']]->resource('recorder')->ids($rec_file['file_name'])->delete();
            }
        } catch (\Exception $exception) {
            $this->parseException($exception);
        }
        return false;
    }
    public function getTasks()
    {
        $deferred_records = $this->getAllDeferredRecords();
        $tasks = [];
        foreach ($deferred_records as $record) {
            if (!$record['started']) {
                $tasks[] = ['id' => $record['id'], 'job' => 'start', 'time' => $record['start_ts']];
            }
            $tasks[] = ['id' => $record['id'], 'job' => 'stop', 'time' => $record['stop_ts']];
        }
        return $tasks;
    }
    public function getAllDeferredRecords()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('users_rec.*, UNIX_TIMESTAMP(t_start) as start_ts, UNIX_TIMESTAMP(t_stop) as stop_ts')->from('users_rec')->where(['ended' => 0])->get()->all();
    }
    public function getRecordingInfo($file_id)
    {
        $user_record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users_rec')->where(['file_id' => $file_id])->get()->first();
        if (empty($user_record)) {
            return;
        }
        return ['id' => $user_record['id'], 'start' => \strtotime($user_record['t_start']), 'stop' => \strtotime($user_record['t_stop'])];
    }
    public function getDeferredRecordIdsForUser($uid)
    {
        $user_recs = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('id, program_id, program_real_id')->from('users_rec')->where(['uid' => $uid])->get()->all();
        $rec_ids = [];
        foreach ($user_recs as $record) {
            $rec_ids[$record['program_real_id']] = $record['id'];
        }
        return $rec_ids;
    }
    protected function getAllActiveStorages()
    {
        $storages = [];
        $data = $this->db->from('storages')->where(['status' => 1, 'for_records' => 1])->get()->all();
        foreach ($data as $idx => $storage) {
            $storages[$storage['storage_name']] = $storage;
            $storages[$storage['storage_name']]['load'] = $this->getStorageLoad($storage);
        }
        $storages = $this->sortByLoad($storages);
        return $storages;
    }
    protected function getMediaName()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('rec_files')->where(['id' => $this->media_params['file_id']])->get()->first('file_name');
    }
}
