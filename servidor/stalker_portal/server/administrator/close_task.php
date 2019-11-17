<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Video;
use Ministra\Lib\VideoMaster;
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
if (@$_GET['id']) {
    $task_id = (int) $_GET['id'];
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
    $task = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('moderator_tasks')->where(['id' => $task_id])->get()->first();
    $moderator_id = $task['to_usr'];
    $video_id = $task['media_id'];
    $action = "<a href=\\'msgs.php?task={$task_id}\\'>" . \_('task done') . '</a>';
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('video_log', ['action' => $action, 'video_id' => $video_id, 'moderator_id' => $moderator_id, 'actiontime' => 'NOW()']);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('moderator_tasks', ['ended' => 1, 'end_time' => 'NOW()'], ['id' => (int) $_GET['id']]);
    $video = \Ministra\Lib\Video::getById($video_id);
    $path = $video['path'];
    $master = new \Ministra\Lib\VideoMaster();
    try {
        $master->startMD5SumInAllStorages($path);
    } catch (\Exception $exception) {
    }
    \header('Location: tasks.php');
    exit;
}
