<?php

require __DIR__ . '/common.php';
use Ministra\Lib\Itv;
if (empty($_GET['token'])) {
    \header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
    exit;
}
$uid = \Ministra\Lib\Itv::checkTemporaryLink($_GET['token']);
if (!$uid) {
    \header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
} else {
    \header('X-AuthDuration: 36000');
    \header('X-Unique: true');
    \header('X-Max-Sessions: ' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('max_local_recordings', 10));
    \header('X-UserId: ' . $uid);
    \header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
}
