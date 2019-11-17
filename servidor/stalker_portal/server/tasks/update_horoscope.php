<?php

\error_reporting(\E_ALL);
require __DIR__ . '/common.php';
use Ministra\Lib\Horoscope;
$horoscope = new \Ministra\Lib\Horoscope();
$horoscope->getDataFromRSS();
