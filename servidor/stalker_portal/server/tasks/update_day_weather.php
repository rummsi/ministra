<?php

\error_reporting(\E_ALL);
require __DIR__ . '/common.php';
use Ministra\Lib\Gismeteo;
$weather = new \Ministra\Lib\Gismeteo();
$weather->getDataFromXML();
