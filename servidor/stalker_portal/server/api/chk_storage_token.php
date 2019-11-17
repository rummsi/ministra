<?php

require __DIR__ . '/common.php';
use Ministra\Lib\Master;
$response = ['result' => \Ministra\Lib\Master::checkAccessToken($_GET['token'])];
echo \json_encode($response);
