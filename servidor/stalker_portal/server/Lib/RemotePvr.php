<?php

namespace Ministra\Lib;

use Exception;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class RemotePvr extends \Ministra\Lib\AjaxResponse implements \Ministra\Lib\StbApi\RemotePvr
{
    private static $streamRecorder;
    public function __construct()
    {
        parent::__construct();
    }
    public static function getStreamRecorder()
    {
        return static::$streamRecorder ?: (static::$streamRecorder = new \Ministra\Lib\StreamRecorder());
    }
    public static function delAllUserRecs($user_id)
    {
        $remote_recordings = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users_rec')->where(['uid' => $user_id, 'local' => 0])->get()->all();
        foreach ($remote_recordings as $recording) {
            if ($recording['ended'] == 1) {
                static::stopRecById($recording['id']);
            }
            static::delRecById($recording['id']);
        }
    }
    public static function setAllowedStoragesForChannel($ch_id, $storage_names = array())
    {
        $current_storages = \array_keys(static::getStoragesForChannel($ch_id));
        $need_to_delete = \array_values(\array_diff($current_storages, $storage_names));
        $need_to_add = \array_values(\array_diff($storage_names, $current_storages));
        if (!empty($need_to_delete)) {
            $quoted_storage_name = \array_map(function ($name) {
                return '"' . $name . '"';
            }, $need_to_delete);
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('delete from pvr_storages where ch_id=' . (int) $ch_id . ' and storage_name in (' . \implode(', ', $quoted_storage_name) . ')');
        }
        if (!empty($need_to_add)) {
            $need_to_add = \array_map(function ($task) use($ch_id) {
                return ['ch_id' => $ch_id, 'storage_name' => $task];
            }, $need_to_add);
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('pvr_storages', $need_to_add);
        }
    }
    public static function getStoragesForChannel($ch_id)
    {
        $allowed_storages_raw = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('pvr_storages')->where(['ch_id' => $ch_id])->get()->all();
        $allowed_storages = [];
        if ($allowed_storages_raw) {
            foreach ($allowed_storages_raw as $task) {
                $allowed_storages[$task['storage_name']] = $task;
            }
        }
        return $allowed_storages;
    }
    public function createLink()
    {
        \preg_match("/\\/media\\/(\\d+).mpg\$/", $_REQUEST['cmd'], $tmp_arr);
        $media_id = $tmp_arr[1];
        $item = static::getById($media_id);
        if ($item['local']) {
            return ['cmd' => $item['file'], 'local' => 1];
        }
        $res = $this->getLinkByRecId($media_id);
        if (!empty($res['storage_id'])) {
            $storage = \Ministra\Lib\Master::getStorageById($res['storage_id']);
        }
        if (!empty($storage)) {
            $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
            $cache->set($this->stb->id . '_playback', ['type' => 'npvr', 'id' => $media_id, 'storage' => $storage['storage_name'], 'storage_id' => $storage['id']], 0, 10);
        } else {
            $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\b34ac3b48e9ea7427852f461cb9db6b76::getInstance();
            $cache->del($this->stb->id . '_playback');
        }
        return $res;
    }
    public static function getById($id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('users_rec.*, rec_files.storage_name as storage_name, itv.name as ch_name')->from('users_rec')->join('rec_files', 'users_rec.file_id', 'rec_files.id', 'LEFT')->join('itv', 'users_rec.ch_id', 'itv.id', 'LEFT')->where(['users_rec.id' => (int) $id])->get()->first();
    }
    private function getLinkByRecId($rec_id)
    {
        $item = static::getById($rec_id);
        $master = static::getStreamRecorder();
        try {
            $res = $master->play($rec_id, 0, false, $item['storage_name']);
        } catch (\Exception $e) {
            \trigger_error($e->getMessage());
        }
        $res['local'] = 0;
        if (!empty($res['cmd'])) {
            $res['to_file'] = \Ministra\Lib\System::transliterate($item['id'] . '_' . \Ministra\Lib\Itv::getChannelNameById($item['ch_id']) . '_' . $item['program']);
            if (\preg_match("/\\.(\\w*)\$/", $res['cmd'], $ext_arr)) {
                $res['to_file'] .= '.' . $ext_arr[1];
            }
        }
        if (!empty($_REQUEST['download'])) {
            $downloads = new \Ministra\Lib\Downloads();
            $res['cmd'] = $downloads->createDownloadLink('pvr', $rec_id, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id);
        }
        return $res;
    }
    public function getOrderedList()
    {
        $result = $this->prepareQuery()->where(['uid' => $this->stb->id])->limit(static::MAX_PAGE_ITEMS, $this->page * static::MAX_PAGE_ITEMS);
        $this->setResponseData($result);
        $recorder = static::getStreamRecorder();
        $rest_length = $recorder->checkTotalUserRecordsLength($this->stb->id);
        $this->response['records_rest_length'] = $rest_length;
        return $this->getResponse('prepareData');
    }
    public function prepareQuery()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('users_rec.*, itv.name as ch_name, UNIX_TIMESTAMP(t_start) as t_start_ts')->from('users_rec')->join('itv', 'itv.id', 'users_rec.ch_id', 'LEFT')->orderby('t_start', 'DESC')->orderby('t_stop', 'DESC');
    }
    public function startRecDeferred()
    {
        $response = [];
        try {
            $response['data'] = $this->startRecDeferredById($_REQUEST['program_id']);
        } catch (\Ministra\Lib\NPVRException $e) {
            $response['error'] = \_($e->getMessage());
        }
        return $response;
    }
    public function startRecDeferredById($program_id)
    {
        $recorder = static::getStreamRecorder();
        return $recorder->startDeferred($program_id);
    }
    public function stopRecDeferred()
    {
        $rec_id = \array_key_exists('data', $_REQUEST) ? (int) $_REQUEST['data'] : (\array_key_exists('rec_id', $_REQUEST) ? (int) $_REQUEST['rec_id'] : 0);
        $duration = (int) $_REQUEST['duration'];
        $recorder = static::getStreamRecorder();
        return $recorder->stopDeferred($rec_id, $duration);
    }
    public function startRecNow()
    {
        $response = [];
        try {
            $user_rec_id = $this->startRecNowByChannelId((int) $_REQUEST['ch_id']);
            if ($user_rec_id) {
                $response['data'] = $this->getRecordingChIds(true);
            }
        } catch (\Ministra\Lib\NPVRException $e) {
            $response['error'] = \_($e->getMessage());
        }
        return $response;
    }
    public function startRecNowByChannelId($ch_id)
    {
        $channel = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['id' => $ch_id])->get()->first();
        if (empty($channel)) {
            throw new \Ministra\Lib\NPVRChannelNotFoundError();
        }
        $recorder = static::getStreamRecorder();
        $user_rec_id = $recorder->startNow($channel);
        return $user_rec_id;
    }
    public function getRecordingChIds($only_remote = false)
    {
        $fields = 'id, id as real_id, ch_id, local, UNIX_TIMESTAMP(t_start) as t_start_ts, ' . 'UNIX_TIMESTAMP(t_stop) as t_stop_ts, program, file, program_id, program_real_id, internal_id';
        $remote_recordings = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select($fields)->from('users_rec')->where(['uid' => $this->stb->id, 'ended' => 0, 'started' => 1, 'local' => 0])->get()->all();
        if ($only_remote) {
            return $remote_recordings;
        }
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users_rec', ['ended' => 1, 'started' => 1], ['uid' => $this->stb->id, 'ended' => 0, 'local' => 1, 't_stop<' => 'NOW()']);
        $local_recordings = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select($fields)->from('users_rec')->where(['uid' => $this->stb->id, 'ended' => 0, 'local' => 1])->get()->all();
        return \array_merge($remote_recordings, $local_recordings);
    }
    public function setInternalId()
    {
        $rec_id = (int) $_REQUEST['rec_id'];
        $internal_id = $_REQUEST['internal_id'];
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users_rec', ['internal_id' => $internal_id, 'started' => 1], ['id' => $rec_id]);
    }
    public function startDeferredRecordOnStb()
    {
        $program_id = $_REQUEST['program_real_id'];
        $file = $_REQUEST['file'];
        $internal_id = $_REQUEST['internal_id'];
        $ch_id = (int) $_REQUEST['ch_id'];
        $start_ts = (int) $_REQUEST['start_ts'];
        $stop_ts = (int) $_REQUEST['stop_ts'];
        $recorder = static::getStreamRecorder();
        if ($program_id != 0) {
            $rec_exist = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users_rec')->where(['program_real_id' => $program_id, 'uid' => $this->stb->id])->get()->first();
            if ($rec_exist) {
                return $rec_exist['id'];
            }
            $rec_id = $recorder->startDeferred($program_id, true, ['time' => \date('Y-m-d H:i:s', $start_ts), 'time_to' => \date('Y-m-d H:i:s', $stop_ts)]);
        } else {
            $program = ['id' => 0, 'real_id' => '', 'ch_id' => $ch_id, 'time' => \date('Y-m-d H:i:s', $start_ts), 'time_to' => \date('Y-m-d H:i:s', $stop_ts)];
            $rec_id = $recorder->startDeferred($program_id, true, $program);
        }
        if ($rec_id) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users_rec', ['file' => $file, 'internal_id' => $internal_id], ['id' => $rec_id]);
        }
        return $rec_id;
    }
    public function startRecordOnStb()
    {
        $ch_id = (int) $_REQUEST['ch_id'];
        $file = $_REQUEST['file'];
        $start_ts = (int) $_REQUEST['start_ts'];
        $stop_ts = (int) $_REQUEST['stop_ts'];
        $internal_id = $_REQUEST['internal_id'];
        $channel = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['id' => $ch_id])->get()->first();
        if (empty($channel)) {
            return false;
        }
        $recorder = static::getStreamRecorder();
        $rec_id = $recorder->startNow($channel, true);
        if ($rec_id) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users_rec', ['file' => $file, 't_start' => \date('Y-m-d H:i:s', $start_ts), 't_stop' => \date('Y-m-d H:i:s', $stop_ts), 'length' => $stop_ts - $start_ts, 'internal_id' => $internal_id], ['id' => $rec_id]);
        }
        return $rec_id;
    }
    public function updateRecordOnStbEndTime()
    {
        $rec_id = (int) $_REQUEST['rec_id'];
        $stop_ts = (int) $_REQUEST['stop_ts'];
        $user_record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users_rec')->where(['id' => $rec_id])->get()->first();
        if (empty($user_record)) {
            return false;
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users_rec', ['t_stop' => \date('Y-m-d H:i:s', $stop_ts), 'length' => $stop_ts - \strtotime($user_record['t_start'])], ['id' => $rec_id])->result();
    }
    public function stopRecordOnStb()
    {
        $rec_id = (int) $_REQUEST['rec_id'];
        $user_record = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users_rec')->where(['id' => $rec_id])->get()->first();
        if (empty($user_record)) {
            return false;
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users_rec', ['ended' => '1', 'end_record' => 'NOW()', 'length' => \time() - \strtotime($user_record['t_start'])], ['id' => $rec_id])->result();
    }
    public function delRecordOnStb()
    {
        $rec_id = (int) $_REQUEST['rec_id'];
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('users_rec', ['id' => $rec_id]);
    }
    public function stopRec()
    {
        return static::stopRecById((int) $_REQUEST['rec_id']);
    }
    public static function stopRecById($rec_id)
    {
        $recorder = static::getStreamRecorder();
        return $recorder->stop($rec_id);
    }
    public function getActiveRecordings()
    {
        return $this->getRecordingChIds();
    }
    public function delRec()
    {
        return static::delRecById((int) $_REQUEST['rec_id']);
    }
    public static function delRecById($rec_id)
    {
        $recorder = static::getStreamRecorder();
        return $recorder->del($rec_id);
    }
    public function getUrlByRecId($rec_id)
    {
        $link = $this->getLinkByRecId($rec_id);
        if (empty($link['cmd'])) {
            throw new \Exception('Obtaining url failed');
        }
        return $link['cmd'];
    }
    public function prepareData()
    {
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $this->response['data'][$i]['length'] = \Ministra\Lib\System::convertTimeLengthToHuman($this->response['data'][$i]['length']);
            $this->response['data'][$i]['t_start'] = \Ministra\Lib\System::convertDatetimeToHuman($this->response['data'][$i]['t_start_ts']);
            if ($this->response['data'][$i]['local']) {
                $this->response['data'][$i]['cmd'] = 'auto ' . $this->response['data'][$i]['file'];
            } else {
                $this->response['data'][$i]['cmd'] = 'auto /media/' . $this->response['data'][$i]['id'] . '.mpg';
            }
            if (!empty($this->response['data'][$i]['program'])) {
                $this->response['data'][$i]['ch_name'] .= ' — ' . $this->response['data'][$i]['program'];
            }
            $this->response['data'][$i]['name'] = $this->response['data'][$i]['ch_name'];
            $this->response['data'][$i]['open'] = !$this->response['data'][$i]['ended'];
            $this->response['data'][$i]['started'] = (int) $this->response['data'][$i]['started'];
            $this->response['data'][$i]['ended'] = (int) $this->response['data'][$i]['ended'];
            if ($this->response['data'][$i]['started'] && !$this->response['data'][$i]['ended']) {
                $this->response['data'][$i]['length'] = \_('recording');
            } elseif (!$this->response['data'][$i]['started'] && !$this->response['data'][$i]['ended']) {
                $this->response['data'][$i]['length'] = \_('scheduled');
            }
        }
        return $this->response;
    }
    public function startRecDeferredByChannelId($ch_id, $start_ts, $stop_ts)
    {
        $channel = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['id' => $ch_id])->get()->first();
        if (empty($channel)) {
            throw new \Ministra\Lib\NPVRChannelNotFoundError();
        }
        $program = ['id' => 0, 'real_id' => '', 'ch_id' => $ch_id, 'time' => \date('Y-m-d H:i:s', $start_ts), 'time_to' => \date('Y-m-d H:i:s', $stop_ts)];
        $recorder = static::getStreamRecorder();
        $rec_id = $recorder->startDeferred(0, false, $program);
        return $rec_id;
    }
}
