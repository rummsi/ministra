<?php

require __DIR__ . '/common.php';
use Ministra\Lib\TvArchive;
$response = ['result' => \Ministra\Lib\TvArchive::checkTemporaryToken($_GET['token'])];
echo \json_encode($response);
