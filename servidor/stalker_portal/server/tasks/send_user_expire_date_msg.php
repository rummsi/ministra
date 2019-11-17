<?php

require __DIR__ . '/common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\SysEvent;
$enable_internal_billing = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_internal_billing', \false);
$end_billing_interval = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('number_of_days_to_send_message', \false);
if (!empty($enable_internal_billing) && !empty($end_billing_interval) && \is_numeric($end_billing_interval)) {
    $locales = [];
    $allowed_locales = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('allowed_locales');
    $default_locale = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('default_locale');
    \bindtextdomain('stb', \PROJECT_PATH . '/locale');
    \textdomain('stb');
    \bind_textdomain_codeset('stb', 'UTF-8');
    $users = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select(['id', '(TO_DAYS(`expire_billing_date`) - TO_DAYS(NOW())) as `end_billing_days`', 'locale'])->from('`users`')->where(["(TO_DAYS(`expire_billing_date`) - TO_DAYS(NOW())) <= '{$end_billing_interval}' " . "AND CAST(`expire_billing_date` AS CHAR) <> '0000-00-00 00:00:00' AND 1=" => 1, 'status' => 0])->get()->all();
    $event = new \Ministra\Lib\SysEvent();
    $event->setTtl(86340);
    $msg_more = 'Dear Subscriber! Your payment term will expire in "%s" days. ' . 'Please refill a personal account in order to avoid tripping of services.';
    $msg_today = 'Dear Subscriber! Your payment term will expire today. ' . 'Please refill a personal account in order to avoid tripping of services.';
    $msg = '';
    $locale = $default_locale;
    foreach ($users as $user) {
        $current_local = \setlocale(\LC_MESSAGES, 0);
        $event->setUserListById($user['id']);
        if (\in_array($user['locale'], $allowed_locales)) {
            $locale = $user['locale'];
        } else {
            $locale = $default_locale;
        }
        \putenv("LC_MESSAGES={$locale}");
        \putenv("LANGUAGE={$locale}");
        \setlocale(\LC_MESSAGES, $locale);
        $msg = $user['end_billing_days'] > 0 ? \sprintf(\_($msg_more), $user['end_billing_days']) : \_($msg_today);
        $event->sendMsg($msg);
    }
}
