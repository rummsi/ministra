<?php

$start_script_time = \microtime(\true);
require_once __DIR__ . '/../../admin/app.php';
require __DIR__ . '/../../admin/config/boot_app.php';
if (!isset($app)) {
    throw new \Exception('App variable does not define');
}
try {
    $app->run();
} catch (\Exception $e) {
    \adminLogError($app, $e);
    throw $e;
} catch (\Throwable $e) {
    \adminLogError($app, $e);
    throw $e;
}
if ($app->offsetExists('monolog') && !empty($start_script_time)) {
    $end_script_time = \microtime(\true);
    $app['monolog']->addInfo(\sprintf("Script end timestamp - '%s'", $end_script_time) . \PHP_EOL);
    $app['monolog']->addInfo(\sprintf("Script execution - '%s'", \number_format($end_script_time - $start_script_time, 3, '.', ' ')) . \PHP_EOL);
    $app['monolog']->addInfo(\str_pad('', 80, '-') . \PHP_EOL);
}
