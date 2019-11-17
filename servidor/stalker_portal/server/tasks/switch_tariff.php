<?php

\set_time_limit(0);
require __DIR__ . '/common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\SysEvent;
use Ministra\Lib\User;
if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans')) {
    exit;
}
$need_to_switch = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['tariff_expired_date!=' => '', 'tariff_expired_date != ' => '0000-00-00 00:00:00', 'tariff_expired_date<=' => \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d)])->get();
$tariffs = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->get()->all();
$tariffs_map = [];
foreach ($tariffs as $tariff) {
    $tariffs_map[$tariff['id']] = $tariff;
}
$default_tariff = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['user_default' => 1])->get()->first();
while ($user = $need_to_switch->next()) {
    if (isset($tariffs_map[$user['tariff_id_instead_expired']])) {
        $days_to_expires = $tariffs_map[$user['tariff_id_instead_expired']]['days_to_expires'];
    } elseif ($default_tariff) {
        $days_to_expires = $default_tariff['days_to_expires'];
    }
    $tariff_plan_id = isset($tariffs_map[$user['tariff_id_instead_expired']]) ? $user['tariff_id_instead_expired'] : 0;
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['tariff_plan_id' => $tariff_plan_id, 'tariff_expired_date' => !isset($days_to_expires) || $days_to_expires == 0 ? \null : \date(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::aec39d8e7a39881e5c1c5a8ff5c4b00d, \time() + $days_to_expires * 24 * 3600), 'tariff_id_instead_expired' => 0], ['id' => $user['id']]);
    if ($tariff_plan_id != $user['tariff_plan_id']) {
        $event = new \Ministra\Lib\SysEvent();
        $event->setUserListById([(int) $user['id']]);
        $user_o = \Ministra\Lib\User::getInstance((int) $user['id']);
        $event->sendMsgAndReboot($user_o->getLocalizedText('Tariff plan is changed, please restart your STB'));
        \Ministra\Lib\User::clear();
    }
}
