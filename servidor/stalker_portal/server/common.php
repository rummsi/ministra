<?php

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
\defined('PROJECT_PATH') or \define('PROJECT_PATH', __DIR__);
require_once __DIR__ . '/../vendor/autoload.php';
$_SERVER['REMOTE_ADDR'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
\set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) {
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115::B735c22e927763311870c2e748ad9bd94($errno, $errstr, $errfile, $errline, $errcontext);
}, \E_ALL);
\set_exception_handler(function (\Throwable $t) {
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115::P33f331824df667fc2c0176fa82f55c39($t);
});
\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::D2ab9a6f149b6432f21a95b05b3405e83();
\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::K93836f46e5e588c0a0cd2ef90890d1db();
\defined('FATAL') or \define('FATAL', \E_USER_ERROR);
\defined('ERROR') or \define('ERROR', \E_USER_WARNING);
\defined('WARNING') or \define('WARNING', \E_USER_NOTICE);
if (!\defined('PATH_SEPARATOR')) {
    \define('PATH_SEPARATOR', \getenv('COMSPEC') ? ';' : ':');
}
\defined('PROJECT_PATH') or \define('PROJECT_PATH', \dirname(__FILE__));
\ini_set('include_path', \ini_get('include_path') . \PATH_SEPARATOR . \PROJECT_PATH);
if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('default_timezone')) {
    \date_default_timezone_set(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('default_timezone'));
}
if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('http_proxy')) {
    $default_context = ['http' => ['proxy' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('http_proxy'), 'request_fulluri' => \true]];
    if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('http_proxy_login') && \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('http_proxy_password')) {
        $default_context['http']['header'] = 'Proxy-Authorization: Basic ' . \base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('http_proxy_login') . ':' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('http_proxy_password')) . "\r\n";
    }
    \stream_context_set_default($default_context);
    \libxml_set_streams_context(\stream_context_create($default_context));
}
