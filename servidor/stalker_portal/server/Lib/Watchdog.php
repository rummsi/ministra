<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class Watchdog extends \Ministra\Lib\AjaxResponse implements \Ministra\Lib\StbApi\Watchdog
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getEvents()
    {
        $just_started = isset($_REQUEST['init']) ? (int) $_REQUEST['init'] : 0;
        if (isset($_REQUEST['init']) && \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('log_mac_clones', false) && $just_started == 0 && \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->R35cd2e80d7a2fc41598228f4269aed88('just_started') == 0) {
            $clone_ip = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::n68312a10d430a8b53586d69560c8b609();
            if ($clone_ip) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::f9351ecb71f21bd0ff2f10fc16d271a8($clone_ip);
            }
        }
        if ($this->stb->R35cd2e80d7a2fc41598228f4269aed88('ip') != $this->stb->ip) {
            $user = \Ministra\Lib\User::getInstance($this->stb->id);
            $user->getInfoFromOSS();
        }
        $this->db->update('users', ['keep_alive' => 'NOW()', 'ip' => $this->stb->ip, 'now_playing_type' => (int) $_REQUEST['cur_play_type'], 'just_started' => $just_started, 'last_watchdog' => 'NOW()'], ['mac' => $this->stb->mac]);
        $events = \Ministra\Lib\Event::getAllNotEndedEvents($this->stb->id);
        $messages = \is_array($events) ? \count($events) : 0;
        $res['data'] = [];
        $res['data']['msgs'] = $messages;
        if ($messages > 0) {
            if ($events[0]['sended'] == 0) {
                \Ministra\Lib\Event::setSended($events[0]['id']);
                if ($events[0]['need_confirm'] == 0) {
                    \Ministra\Lib\Event::setEnded($events[0]['id']);
                }
            }
            if ($events[0]['id'] != @$_GET['data']['event_active_id']) {
                $res['data']['id'] = $events[0]['id'];
                $res['data']['event'] = $events[0]['event'];
                $res['data']['need_confirm'] = $events[0]['need_confirm'];
                $res['data']['msg'] = $events[0]['msg'];
                $res['data']['reboot_after_ok'] = $events[0]['reboot_after_ok'];
                $res['data']['auto_hide_timeout'] = $events[0]['auto_hide_timeout'];
                $res['data']['post_function'] = $events[0]['post_function'];
                $res['data']['param1'] = $events[0]['param1'];
                $res['data']['valid_until'] = \strtotime($events[0]['eventtime']);
                if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('display_send_time_in_message', false)) {
                    $res['data']['send_time'] = $events[0]['addtime'];
                }
            }
        }
        $res['data']['additional_services_on'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tariff_plans', false) ? '1' : $this->stb->additional_services_on;
        return $res;
    }
    public function confirmEvent()
    {
        \Ministra\Lib\Event::setConfirmed((int) $_REQUEST['event_active_id']);
        $res['data'] = 'ok';
        return $res;
    }
}
