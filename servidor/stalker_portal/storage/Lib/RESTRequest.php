<?php

namespace Ministra\Storage\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class RESTRequest extends \Ministra\Storage\Lib\APIRequest
{
    private $action;
    private $resource;
    private $identifiers;
    private $access_token;
    private $data;
    private static $use_mac_identifiers = false;
    public function __construct()
    {
        $this->init();
    }
    protected function init()
    {
        if (empty($_SERVER['REQUEST_METHOD'])) {
            throw new \Ministra\Storage\Lib\RESTRequestException('Empty request method');
        }
        if (empty($_GET['q'])) {
            throw new \Ministra\Storage\Lib\RESTRequestException('Empty resource');
        }
        $this->parseAuthorizationHeader();
        $requested_uri = $_GET['q'];
        $params = \explode('/', $requested_uri);
        if (empty($params[\count($params) - 1])) {
            unset($params[\count($params) - 1]);
        }
        if (\count($params) == 0) {
            throw new \Ministra\Storage\Lib\RESTRequestException('Empty resource');
        }
        $this->resource = $params[0];
        if (\count($params) > 1) {
            $this->identifiers = \explode(',', $params[1]);
        }
        $method = \strtolower($_SERVER['REQUEST_METHOD']);
        $methods_map = ['get' => 'get', 'post' => 'create', 'put' => 'update', 'delete' => 'delete'];
        if (empty($methods_map[$method])) {
            throw new \Ministra\Storage\Lib\RESTRequestException('Not supported method');
        }
        $this->action = $methods_map[$method];
        \parse_str(\file_get_contents('php://input'), $this->data);
    }
    private function parseAuthorizationHeader()
    {
        if (\function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            $headers = $this->getHttpHeaders();
        }
        if (!$headers) {
            return;
        }
        $auth_header = !empty($headers['Authorization']) ? $headers['Authorization'] : null;
        if ($auth_header && \preg_match('/Bearer\\s+(.*)$/i', $auth_header, $matches)) {
            $this->access_token = \trim($matches[1]);
        }
    }
    private function getHttpHeaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (\substr($name, 0, 5) == 'HTTP_') {
                $headers[\str_replace(' ', '-', \ucwords(\strtolower(\str_replace('_', ' ', \substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
    public function getAction()
    {
        return $this->action;
    }
    public function getResource()
    {
        return $this->resource;
    }
    public function getIdentifiers()
    {
        return $this->identifiers;
    }
    public function getAccessToken()
    {
        return $this->access_token;
    }
    public static function useMacIdentifiers()
    {
        return self::$use_mac_identifiers = true;
    }
    public function getConvertedIdentifiers()
    {
        if (self::$use_mac_identifiers) {
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::J52a70d742695481c6be0069bd3ada898($this->identifiers);
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::d843f1b4e8b6eb07028c421714188551($this->identifiers);
    }
    public function getData($key = '')
    {
        if (!empty($key)) {
            if (!\array_key_exists($key, $this->data)) {
                return;
            }
            return $this->data[$key];
        }
        return $this->data;
    }
}
