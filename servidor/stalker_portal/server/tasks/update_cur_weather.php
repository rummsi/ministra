<?php

\error_reporting(\E_ALL);
require __DIR__ . '/common.php';
use Ministra\Lib\Curweather;
$cur_weather = new \Ministra\Lib\Curweather();
$cur_weather->getDataFromGAPI();
