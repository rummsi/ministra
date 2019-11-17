<?php

require_once __DIR__ . '/lib/config.php';
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
\Ministra\Lib\Admin::checkAuth();
$JsHttpRequest = new \Subsys_JsHttpRequest_Php('utf-8');
$_RESULT = \Ministra\OldAdmin\get_data();
echo '<b>REQUEST_URI:</b> ' . $_SERVER['REQUEST_URI'] . '<br>';
