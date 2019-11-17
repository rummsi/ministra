<?php

require_once '../server/common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_soap_api', \false)) {
    \header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
    echo 'SOAP API is not enabled';
    exit;
}
require_once __DIR__ . '/../vendor/rock/phpwsdl/src/Rock/PhpWsdl/class.phpwsdl.php';
use Ministra\Lib\SOAPApi\v1\SoapApiServer;
$api_server = new \Ministra\Lib\SOAPApi\v1\SoapApiServer();
if (isset($_GET['wsdl'])) {
    $api_server->outputWsdl();
} elseif (isset($_GET['docs'])) {
    $api_server->outputDocs();
} elseif (isset($_GET['phpsoapclient'])) {
    $api_server->outputPhpClient();
} else {
    $api_server->handleRequest();
}
