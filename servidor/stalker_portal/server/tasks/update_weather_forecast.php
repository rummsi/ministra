<?php

\error_reporting(\E_ALL);
\set_time_limit(0);
\sleep(\rand(300, 600));
require __DIR__ . '/common.php';
use Ministra\Lib\Weather;
$weather = new \Ministra\Lib\Weather();
$weather->updateFullForecast();
