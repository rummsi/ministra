<?php

require __DIR__ . '/common.php';
use Ministra\Lib\AppsManager;
use Ministra\Lib\SmartLauncherAppsManager;
\set_time_limit(0);
$apps = new \Ministra\Lib\AppsManager();
$apps->startAutoUpdate();
$launcher_apps = new \Ministra\Lib\SmartLauncherAppsManager();
$launcher_apps->startAutoUpdate();
$launcher_apps->syncApps();
$launcher_apps->updateAllAppsInfo();
