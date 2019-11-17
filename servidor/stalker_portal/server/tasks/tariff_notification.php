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
$default_tariff = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['user_default' => 1])->get()->first();
$tariffs_notifications = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('tariffs_notifications.*, messages_templates.header, messages_templates.body, messages_templates.url')->from('tariffs_notifications')->join('messages_templates', 'tariffs_notifications.template_id', 'messages_templates.id', 'INNER')->get()->all();
$tariffs_notifications_map = [];
foreach ($tariffs_notifications as $tariffs_notification) {
    if (!isset($tariffs_notifications_map[$tariffs_notification['tariff_id']])) {
        $tariffs_notifications_map[$tariffs_notification['tariff_id']] = [];
    }
    $tariffs_notifications_map[$tariffs_notification['tariff_id']][] = $tariffs_notification;
}
$users = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['tariff_expired_date!=' => '', 'tariff_expired_date != ' => '0000-00-00 00:00:00'])->get();
while ($user = $users->next()) {
    if ($user['tariff_plan_id'] == 0 && !$default_tariff) {
        continue;
    }
    $tariff_plan_id = $user['tariff_plan_id'] == 0 ? $default_tariff['id'] : $user['tariff_plan_id'];
    if (isset($tariffs_notifications_map[$tariff_plan_id])) {
        foreach ($tariffs_notifications_map[$tariff_plan_id] as $notification) {
            $tariff_expired_time = \strtotime($user['tariff_expired_date']);
            if ($tariff_expired_time < \time() + $notification['notification_delay_in_hours'] * 3600 && $tariff_expired_time > \time() + ($notification['notification_delay_in_hours'] - 1) * 3600) {
                $event = new \Ministra\Lib\SysEvent();
                $event->setUserListById([(int) $user['id']]);
                $user_o = \Ministra\Lib\User::getInstance((int) $user['id']);
                if ($notification['url']) {
                    $event->sendMsgWithUrl($notification['body'], $notification['url'], $notification['header']);
                } else {
                    $event->sendMsg($notification['body'], $notification['header']);
                }
                \Ministra\Lib\User::clear();
            }
        }
    }
}
