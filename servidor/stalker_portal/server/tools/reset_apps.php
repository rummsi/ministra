<?php

if (\PHP_SAPI != 'cli') {
    exit;
}
require __DIR__ . '/../common.php';
use Ministra\Lib\SmartLauncherAppsManager;
use Ministra\Lib\SmartLauncherAppsManagerConflictException;
$apps_manager = new \Ministra\Lib\SmartLauncherAppsManager();
try {
    $apps_manager->resetApps();
} catch (\Ministra\Lib\SmartLauncherAppsManagerConflictException $e) {
    echo $e->getMessage() . "\n";
    $conflicts = $e->getConflicts();
    foreach ($conflicts as $conflict) {
        echo "\tApplication: " . $conflict['target'] . ":\n";
        echo "\t\tDependency: " . $conflict['alias'] . "\n";
        echo "\t\tExpression: " . $conflict['current_version'] . "\n";
    }
}
