<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class Log
{
    private static $instance = null;
    private $db;
    private $stb;
    public function __construct()
    {
        $this->db = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        $this->stb = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance();
    }
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public static function writePackageSubscribeLog($user_id, $package_id, $set_state)
    {
        $data = ['user_id' => $user_id, 'set_state' => $set_state, 'package_id' => $package_id];
        if (!empty(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id) && (empty($_SERVER['TARGET']) || $_SERVER['TARGET'] !== 'API' && $_SERVER['TARGET'] !== 'ADM')) {
            $data['initiator_id'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->id;
            $data['initiator'] = 'user';
        } else {
            $data['initiator_id'] = \Ministra\Lib\Admin::getInstance()->getId();
            if (!empty($data['initiator_id'])) {
                $data['initiator'] = 'admin';
            }
        }
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('package_subscribe_log', $data);
    }
    public function savePageGenerationTime($time)
    {
        $time = $time * 1000;
        if ($time >= 500) {
            $default_row = '500ms';
        } elseif ($time >= 400) {
            $default_row = '400ms';
        } elseif ($time >= 300) {
            $default_row = '300ms';
        } elseif ($time >= 200) {
            $default_row = '200ms';
        } elseif ($time >= 100) {
            $default_row = '100ms';
        } else {
            $default_row = '0ms';
        }
        $item = $this->db->from('generation_time')->where(['time' => $default_row])->get()->first();
        $this->db->update('generation_time', ['counter' => $item['counter'] + 1], ['time' => $default_row]);
    }
}
