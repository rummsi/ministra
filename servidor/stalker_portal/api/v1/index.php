<?php

require_once '../../server/common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\RESTAPI\v1\RESTManager;
if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_api', \false) && \strpos($_SERVER['QUERY_STRING'], 'tv_archive') != 2 && \strpos($_SERVER['QUERY_STRING'], 'stream_recorder') != 2 && \strpos($_SERVER['QUERY_STRING'], 'monitoring_links') != 2 && \strpos($_SERVER['QUERY_STRING'], 'tv_tmp_link') != 2) {
    \header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    echo 'API not enabled';
    exit;
}
\Ministra\Lib\RESTAPI\v1\RESTManager::setAuthParams(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('api_auth_login', ''), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('api_auth_password', ''));
\Ministra\Lib\RESTAPI\v1\RESTManager::enableLogger(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_api_log', \false));
\Ministra\Lib\RESTAPI\v1\RESTManager::handleRequest();
