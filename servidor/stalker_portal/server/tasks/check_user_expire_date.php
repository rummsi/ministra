<?php

namespace Ministra\Tasks;

require __DIR__ . '/common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39;
use Ministra\Lib\SysEvent;
if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_internal_billing', false)) {
    $ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('`users`')->where(['(TO_DAYS(`expire_billing_date`) - TO_DAYS(NOW()) - 1) < 0 AND ' . "CAST(`expire_billing_date` AS CHAR) <> '0000-00-00 00:00:00' AND 1=" => 1, 'status' => 0])->get()->all('id');
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('`users`', ['status' => 1, 'last_change_status' => 'NOW()'], [" `id` IN ('" . \implode("', '", $ids) . "') AND 1=" => 1, 'status' => 0]);
    $online = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::b03d7ef7b2ba1705d9a43de730650d5f();
    $event = new \Ministra\Lib\SysEvent();
    $event->setUserListById(\array_intersect($ids, $online));
    $event->sendCutOff();
}
