<?php

require __DIR__ . '/common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Video;
$today_tasks = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video_on_tasks')->where(['date_on<=' => 'CURDATE()'])->get()->all();
foreach ($today_tasks as $task) {
    try {
        \Ministra\Lib\Video::switchOnById($task['video_id'], \true);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('video_on_tasks', ['id' => $task['id']]);
    } catch (\Exception $e) {
        echo $e->getTraceAsString();
    }
}
