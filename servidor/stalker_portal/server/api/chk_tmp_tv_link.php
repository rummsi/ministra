<?php

require __DIR__ . '/common.php';
use Ministra\Lib\Itv;
$result = \Ministra\Lib\Itv::checkTemporaryLink($_GET['key']);
if (!$result) {
    $result = '/404/';
}
\header('X-Accel-Redirect: ' . $result);
