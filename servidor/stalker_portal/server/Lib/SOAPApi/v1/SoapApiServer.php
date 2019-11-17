<?php

namespace Ministra\Lib\SOAPApi\v1;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
class SoapApiServer
{
    private $server;
    private $handler = 'Ministra\\Lib\\SOAPApi\\v1\\SoapApiHandler';
    public function __construct()
    {
        \ini_set('soap.wsdl_cache_enabled', '0');
    }
    public function handleRequest()
    {
        $this->server = new \SoapServer(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('wsdl_uri'), ['cache_wsdl' => WSDL_CACHE_NONE]);
        $this->server->setClass($this->handler);
        $this->server->handle();
    }
    public function output($doc = true, $wsdl = false, $phpclient = false)
    {
        $soap = \PhpWsdl::CreateInstance(
            'API',
            // PhpWsdl will determine a good namespace
            null,
            // Change this to your SOAP endpoint URI (or keep it NULL and PhpWsdl will determine it)
            null,
            // Change this to a folder with write access
            [
                // All files with WSDL definitions in comments
                PROJECT_PATH . '/Lib/SOAPApi/v1/SoapApiHandler.php',
                PROJECT_PATH . '/Lib/SOAPApi/v1/StringArray.php',
                PROJECT_PATH . '/Lib/SOAPApi/v1/AccountInfo.php',
                PROJECT_PATH . '/Lib/SOAPApi/v1/Account.php',
                PROJECT_PATH . '/Lib/SOAPApi/v1/SubscriptionAction.php',
                PROJECT_PATH . '/Lib/SOAPApi/v1/SearchCondition.php',
            ],
            null,
            // The name of the class that serves the webservice will be determined by PhpWsdl
            null,
            // This demo contains all method definitions in comments
            null,
            // This demo contains all complex types in comments
            false,
            // Don't send WSDL right now
            false
        );
        // Don't start the SOAP server right now
        // Disable caching for demonstration
        \ini_set('soap.wsdl_cache_enabled', 0);
        // Disable caching in PHP
        \PhpWsdl::$CacheTime = 0;
        // Disable caching in PhpWsdl
        if ($wsdl) {
            $soap->ForceOutputWsdl = true;
            $soap->Optimize = false;
        } else {
            if ($phpclient) {
                $soap->ForceOutputPhp = true;
            } else {
                $soap->ForceOutputHtml = true;
            }
        }
        echo $soap->RunServer();
    }
    public function outputWsdl()
    {
        $this->output(false, true);
    }
    public function outputPhpClient()
    {
        $this->output(false, false, true);
    }
    public function outputDocs()
    {
        $this->output(true);
    }
}
