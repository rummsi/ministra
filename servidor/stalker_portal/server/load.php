<?php

require_once __DIR__ . '/common.php';
use Ministra\Lib\AjaxBackend;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\E6179631c3e38b4304ae50aa6d937d286;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\t9da99a3480e53ad517ce33aca18b17c3;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
use Ministra\Lib\Debug;
$start_time = \microtime(1);
\header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
\header('Last-Modified: Thu, 01 Jan 1970 00:00:00 GMT');
\header('Pragma: no-cache');
\header('Cache-Control: no-store, no-cache, must-revalidate');
\set_error_handler([$debug = \Ministra\Lib\Debug::getInstance(), 'parsePHPError']);
$response = new \Ministra\Lib\AjaxBackend();
\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance();
$loader = new \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\E6179631c3e38b4304ae50aa6d937d286($_REQUEST['type'], $_REQUEST['action']);
try {
    $loader->d92d4642b788d11c28d572779d63ef18();
} catch (\Exception $e) {
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115::q6ee195c1759171b9aef09286fb44db47($e->getMessage());
}
$response->setBody($loader->getResult());
\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\t9da99a3480e53ad517ce33aca18b17c3::d5de025803f2de6a57d75fa98ac892b8c('Begin classic launcher');
echo 'generated in: ' . \round(\microtime(1) - $start_time, 3) . 's; query counter: ' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::get_num_queries() . '; cache hits: ' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::get_cache_hits() . '; cache miss: ' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::get_cache_misses() . '; ' . $debug->getErrorStr();
$response->send();
