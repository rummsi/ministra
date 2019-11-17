<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class Event extends \Ministra\Lib\HTTPPush
{
    public static $allowed_events = array('send_msg', 'reboot', 'reload_portal', 'update_channels', 'play_channel', 'play_radio_channel', 'mount_all_storages', 'cut_off', 'update_image', 'update_epg', 'update_subscription', 'update_modules', 'cut_on', 'show_menu', 'additional_services_status', 'send_msg_with_video', 'send_msg_with_url');
    private $param = array('user_list' => array(), 'event' => '', 'header' => '', 'priority' => 0, 'msg' => '', 'need_confirm' => 0, 'reboot_after_ok' => 0, 'eventtime' => 0, 'auto_hide_timeout' => 0, 'param1' => '', 'post_function' => '');
    private $pattern;
    private $db;
    private $ttl;
    public function __construct()
    {
        $this->db = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        $this->pattern = $this->param;
    }
    public static function setSended($id)
    {
        $db = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        $db->update('events', ['sended' => 1], ['id' => $id]);
    }
    public static function setConfirmed($id)
    {
        $db = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        $db->update('events', ['confirmed' => 1, 'ended' => 1], ['id' => $id]);
    }
    public static function setEnded($id)
    {
        $db = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        $db->update('events', ['ended' => 1], ['id' => $id]);
    }
    public static function getAllNotEndedEvents($uid)
    {
        if ($uid) {
            $db = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
            return $db->from('events')->where(['uid' => $uid, 'ended' => 0, 'eventtime>' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d)])->orderby('priority')->orderby('addtime')->get()->all();
        }
        return false;
    }
    public function setUserListByMac($list)
    {
        if (\is_string($list) || \is_int($list)) {
            if ($list == 'all') {
                $this->param['user_list'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::b49b5de1f8caa1ded0e5d2b1848b3a8a();
            } elseif ($list == 'online') {
                $this->param['user_list'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::b03d7ef7b2ba1705d9a43de730650d5f();
            } else {
                $this->param['user_list'] = [\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::e2007d6bdd1c5d517d04d4fdf5eac8bb($list)];
            }
        } else {
            $this->param['user_list'] = [];
            foreach ($list as $mac) {
                $this->param['user_list'][] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::e2007d6bdd1c5d517d04d4fdf5eac8bb($mac);
            }
        }
    }
    public function setUserListById($list)
    {
        if (\is_string($list) || \is_int($list)) {
            if ($list == 'all') {
                $this->param['user_list'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::b49b5de1f8caa1ded0e5d2b1848b3a8a();
            } else {
                $this->param['user_list'] = [$list];
            }
        } else {
            $this->param['user_list'] = $list;
        }
    }
    public function setAutoHideTimeout($timeout)
    {
        $this->param['auto_hide_timeout'] = $timeout;
    }
    public function setTtl($ttl)
    {
        $this->ttl = (int) $ttl;
    }
    protected function setEvent($event)
    {
        $this->param['event'] = $event;
    }
    protected function setMsg($msg)
    {
        $this->param['msg'] = $msg;
    }
    protected function setHeader($header = '')
    {
        $this->param['header'] = $header;
    }
    protected function setParam1($param1)
    {
        $this->param['param1'] = $param1;
    }
    protected function setPostFunction($post_function)
    {
        $this->param['post_function'] = $post_function;
    }
    protected function setNeedConfirm($need_confirm)
    {
        $this->param['need_confirm'] = $need_confirm;
    }
    protected function setRebootAfterOk($reboot_after_ok)
    {
        $this->param['reboot_after_ok'] = $reboot_after_ok;
    }
    protected function send()
    {
        if (!$this->param['eventtime']) {
            if (empty($this->ttl)) {
                if ($this->param['event'] == 'send_msg' || $this->param['event'] == 'send_msg_with_video') {
                    $this->ttl = 7 * 24 * 3600;
                } else {
                    $this->ttl = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2;
                }
            }
            $this->setEventTime(\date('Y-m-d H:i:s', \time() + $this->ttl));
        }
        if (!$this->param['priority']) {
            if ($this->param['event'] == 'send_msg' || $this->param['event'] == 'send_msg_with_video') {
                $this->setPriority(2);
            } else {
                $this->setPriority(1);
            }
        }
        $this->saveInDb();
        $this->push();
        $this->resetEventOptions();
    }
    protected function setEventTime($eventtime)
    {
        $this->param['eventtime'] = $eventtime;
    }
    protected function setPriority($priority)
    {
        $this->param['priority'] = $priority;
    }
    protected function saveInDb()
    {
        if (\is_array($this->param['user_list']) && \count($this->param['user_list']) > 0) {
            $data = [];
            foreach ($this->param['user_list'] as $uid) {
                $data[] = ['uid' => $uid, 'event' => $this->param['event'], 'header' => $this->param['header'], 'addtime' => 'NOW()', 'eventtime' => $this->param['eventtime'], 'need_confirm' => $this->param['need_confirm'], 'reboot_after_ok' => $this->param['reboot_after_ok'], 'msg' => $this->param['msg'], 'priority' => $this->param['priority'], 'auto_hide_timeout' => $this->param['auto_hide_timeout'], 'param1' => $this->param['param1'], 'post_function' => $this->param['post_function']];
                if ($this->param['event'] == 'cut_off') {
                    \Ministra\Lib\R1f2236e0811d4596a67b2dcc907ac1cb\d3ef7751ac669db6e1e59b557a0ad1c15::eb10a2c028d620063e927ba2ba154182($uid);
                }
            }
            if ($this->param['event'] == 'send_msg' && $this->param['reboot_after_ok'] == 1) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('delete from events where uid in(' . \implode(',', $this->param['user_list']) . ') and event="send_msg" and sended=0 and reboot_after_ok=1');
            }
            $this->db->insert('events', $data);
        }
    }
    protected function resetEventOptions()
    {
        $this->param = $this->pattern;
    }
}
