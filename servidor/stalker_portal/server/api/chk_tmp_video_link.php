<?php

require __DIR__ . '/common.php';
use Ministra\Lib\Master;
$result = \Ministra\Lib\Master::checkTemporaryLink($_GET['key']);
if (!$result) {
    $result = '/404/';
}
\header('X-Accel-Redirect: ' . $result);
