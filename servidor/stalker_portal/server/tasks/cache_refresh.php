<?php

\set_time_limit(0);
require __DIR__ . '/common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\KaraokeMaster;
use Ministra\Lib\VideoMaster;
$updated_video = 0;
$updated_karaoke = 0;
$not_custom_video = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video')->where(['protocol!=' => 'custom'])->get();
while ($video = $not_custom_video->next()) {
    $master = new \Ministra\Lib\VideoMaster();
    $master->getAllGoodStoragesForMediaFromNet($video['id'], 0, \true);
    unset($master);
    ++$updated_video;
}
$not_custom_karaoke = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('karaoke')->where(['protocol!=' => 'custom'])->get();
while ($karaoke = $not_custom_karaoke->next()) {
    $master = new \Ministra\Lib\KaraokeMaster();
    $master->getAllGoodStoragesForMediaFromNet($karaoke['id'], 0);
    unset($master);
    ++$updated_karaoke;
}
echo 1;
