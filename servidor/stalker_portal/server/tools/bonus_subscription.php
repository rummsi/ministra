<?php

require __DIR__ . '/../common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\SysEvent;
use Ministra\Lib\System;
$all_channels = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv')->where(['status' => 1])->get()->all('id');
$all_channels = \Ministra\Lib\System::base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($all_channels));
$result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query("insert into itv_subscription (uid, sub_ch, addtime) (select id, '" . $all_channels . "' as sub_ch, now() from users) on duplicate key update sub_ch=VALUES(sub_ch)")->result();
if ($result) {
    $event = new \Ministra\Lib\SysEvent();
    $event->setUserListByMac('all');
    $event->sendUpdateSubscription();
}
