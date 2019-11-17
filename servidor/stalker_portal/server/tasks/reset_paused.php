<?php

if (\PHP_SAPI != 'cli') {
    exit;
}
\error_reporting(\E_ALL);
require __DIR__ . '/common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\SysEvent;
$uid_arr = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('vclub_paused')->where(['UNIX_TIMESTAMP(pause_time)<' => \time() - 86400])->get()->all('uid');
if (\count($uid_arr) > 0) {
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('delete from vclub_paused where uid in (' . \implode(', ', $uid_arr) . ')');
    $event = new \Ministra\Lib\SysEvent();
    $event->setUserListById($uid_arr);
    $event->sendResetPaused();
}
if (\count($argv) == 1) {
    \sleep(\rand(0, 600));
}
\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::fcac1c5a9068348fb7a4b46484a88c1d();
