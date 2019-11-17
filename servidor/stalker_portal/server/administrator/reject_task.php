<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Video;
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
if (@$_GET['id']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
    $task_id = (int) $_GET['id'];
    $task = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('moderator_tasks')->where(['id' => $task_id])->get()->first();
    if (!empty($task) && $task['ended'] == 0) {
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('moderator_tasks', ['ended' => 1, 'rejected' => 1, 'end_time' => 'NOW()'], ['id' => $task_id]);
        \Ministra\Lib\Video::log($task['media_id'], '<a href="msgs.php?task=' . $task_id . '">' . \_('task rejected') . '</a>');
    }
    if (@$_GET['send_to']) {
        \header('Location: send_to.php?id=' . $_GET['send_to']);
    } else {
        \header('Location: tasks.php');
    }
    exit;
}
