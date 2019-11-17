<?php

require __DIR__ . '/common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
$tmp = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select(['count(id) as `count`', '`reseller_id`'])->from('users')->where(['UNIX_TIMESTAMP(keep_alive)>' => \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2])->groupby('reseller_id')->get()->all();
$users_online = [0 => 0];
foreach ($tmp as $row) {
    if (!\array_key_exists((int) $row['reseller_id'], $users_online)) {
        $users_online[(int) $row['reseller_id']] = 0;
    }
    $users_online[(int) $row['reseller_id']] += $row['count'];
}
$users_online['total'] = \array_sum($users_online);
\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('users_activity', ['users_online' => \json_encode($users_online)]);
