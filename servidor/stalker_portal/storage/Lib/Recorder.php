<?php

namespace Ministra\Storage\Lib;

use DomainException;
use Exception;
use OutOfRangeException;
class Recorder extends \Ministra\Storage\Lib\Storage
{
    public function start($url, $rec_id, $start_delay, $duration)
    {
        $rec_id = (int) $rec_id;
        $this->stop($rec_id);
        $filename = $rec_id . '_' . \date('YmdHis') . '.mpg';
        if (!\preg_match('/:\\/\\//', $url)) {
            throw new \Exception('URL wrong format');
        }
        if (\strpos($url, 'rtp://') !== false || \strpos($url, 'udp://') !== false || \strpos($url, 'http://') !== false && \defined('ASTRA_RECORDER') && ASTRA_RECORDER) {
            if (\defined('ASTRA_RECORDER') && ASTRA_RECORDER) {
                \exec('astra ' . PROJECT_PATH . '/dumpstream.lua' . ' -A ' . $url . ' -s ' . $start_delay . ' -l ' . $duration . ' -c ' . API_URL . 'stream_recorder/' . $rec_id . ' -o ' . RECORDS_DIR . $filename . ' > /dev/null 2>&1 & echo $!', $out);
            } else {
                if (!\preg_match('/:\\/\\/([\\d\\.]+):(\\d+)/', $url, $arr)) {
                    throw new \Exception('URL wrong format');
                }
                $ip = $arr[1];
                $port = $arr[2];
                \exec('nohup ' . getPythonInterpreterName() . ' ' . PROJECT_PATH . '/dumpstream' . ' -a' . $ip . ' -p' . $port . ' -s' . $start_delay . ' -l' . $duration . ' -c' . API_URL . 'stream_recorder/' . $rec_id . ' -o' . RECORDS_DIR . $filename . ' -b' . (\defined('DUMPSTREAM_BUFFERING') ? DUMPSTREAM_BUFFERING : 8) . ' > /dev/null 2>&1 & echo $!', $out);
            }
        } else {
            throw new \DomainException('Not supported protocol');
        }
        if ((int) $out[0] == 0) {
            $arr = \explode(' ', $out[0]);
            $pid = (int) $arr[1];
        } else {
            $pid = (int) $out[0];
        }
        if (empty($pid)) {
            throw new \OutOfRangeException('Not possible to get pid');
        }
        if (!\file_put_contents($this->getRecPidFile($rec_id), $pid)) {
            \posix_kill($pid, \defined('ASTRA_RECORDER') && ASTRA_RECORDER ? 1 : 15);
            throw new \Ministra\Storage\Lib\IOException('PID file is not created');
        }
        return $filename;
    }
    public function stop($rec_id)
    {
        $pid_file = $this->getRecPidFile($rec_id);
        if (!\is_file($pid_file)) {
            return true;
        }
        $pid = (int) \file_get_contents($pid_file);
        if (\posix_kill($pid, 0)) {
            $kill_result = \posix_kill($pid, \defined('ASTRA_RECORDER') && ASTRA_RECORDER ? 1 : 15);
            if (!$kill_result) {
                throw new \Ministra\Storage\Lib\IOException('Kill pid "' . $pid . '" failed on ' . $this->storage_name . ': ' . \posix_strerror(\posix_get_last_error()));
            }
            \unlink($pid_file);
            return $kill_result;
        }
        \unlink($pid_file);
        return true;
    }
    public function updateStopTime($rec_id, $stop_time)
    {
        $pid_file = $this->getRecPidFile($rec_id);
        if (!\is_file($pid_file)) {
            return true;
        }
        $pid = (int) \file_get_contents($pid_file);
        if (\posix_kill($pid, 0)) {
            $kill_result = \posix_kill($pid, 14);
            if (!$kill_result) {
                throw new \Ministra\Storage\Lib\IOException('Send signal to pid "' . $pid . '" failed on ' . $this->storage_name . ': ' . \posix_strerror(\posix_get_last_error()));
            }
            return $kill_result;
        }
        return true;
    }
    public function delete($filename)
    {
        return @\unlink(RECORDS_DIR . \basename($filename));
    }
    private function getRecPidFile($rec_id)
    {
        return '/tmp/rec_' . $this->storage_name . '_' . $rec_id . '.pid';
    }
}
