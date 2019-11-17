<?php

\set_time_limit(0);
\sleep(\rand(0, 300));
require __DIR__ . '/common.php';
use Ministra\Lib\NotificationFeed;
$notifications = new \Ministra\Lib\NotificationFeed();
$notifications->sync();
