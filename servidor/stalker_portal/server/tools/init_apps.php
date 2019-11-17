<?php

if (\PHP_SAPI != 'cli') {
    exit;
}
require __DIR__ . '/../common.php';
use Ministra\Lib\SmartLauncherAppsManager;
$apps_manager = new \Ministra\Lib\SmartLauncherAppsManager();
$apps_manager->initApps();
