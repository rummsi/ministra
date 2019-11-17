<?php

namespace Ministra\Lib\RESTAPI\v1;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\Logger;
class RESTResponse
{
    protected $body = array('status' => 'OK', 'results' => '');
    private $request;
    private $content_type = 'application/json';
    public function __construct()
    {
        \ob_start();
    }
    public function setBody($body)
    {
        $this->body['results'] = $body;
    }
    public function setRequest($request)
    {
        $this->request = $request;
    }
    public function sendAuthRequest()
    {
        \header('WWW-Authenticate: Basic realm="Ministra API"');
        \header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
        $this->setError('401 Unauthorized request');
        $this->send();
        exit;
    }
    public function setError($text)
    {
        $this->body['error'] = $text;
        $this->body['status'] = 'ERROR';
    }
    public function send()
    {
        if (!empty($this->request) && \strpos($this->request->getAccept(), 'text/channel-monitoring-id-url') !== false) {
            if (\is_array($this->body['results'])) {
                $channels = \array_filter($this->body['results'], function ($channel) {
                    return $channel['enable_monitoring'];
                });
                if (\preg_match("/items=(\\d+)-(\\d*)/", $this->request->getAccept(), $match)) {
                    $start = $match[1];
                    $end = empty($match[2]) ? \count($channels) : $match[2];
                    $channels = \array_slice($channels, $start - 1, $end - $start + 1);
                } elseif (\preg_match("/part=(\\d+)\\/(\\d+)/", $this->request->getAccept(), $match)) {
                    $length = \count($channels);
                    $start = \round(((int) $match[1] - 1) * ($length / (int) $match[2]));
                    $end = \round((int) $match[1] * ($length / (int) $match[2]));
                    $channels = \array_slice($channels, $start, $end - $start);
                }
                $body = \array_reduce($channels, function ($prev, $curr) {
                    return $prev . $curr['id'] . ' ' . $curr['url'] . (isset($curr['type']) ? ' ' . $curr['type'] : '') . ' ' . \str_replace(' ', '_', $curr['ch_name']) . ' ' . ((int) $curr['status'] == 1 ? 'up' : 'down') . "\n";
                }, '');
                \header('Content-Type: text/plain');
                echo $body;
            }
            return;
        }
        \header('Content-Type: ' . $this->content_type);
        $this->setOutput();
        $response = \json_encode($this->body);
        echo $response;
        \ob_end_flush();
        if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_api_log', true)) {
            return;
        }
        $logger = new \Ministra\Lib\Logger();
        $logger->setPrefix('api_');
        $logger->access(\sprintf("%s - %s - [%s] %s \"%s\" - \"%s\" %d\n", empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_REAL_IP'], @$_SERVER['PHP_AUTH_USER'], \date('r'), $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], empty($this->request) ? 'no resource' : \http_build_query($this->request->getData()), \strlen($response)));
        if (!empty($this->body['error'])) {
            $logger->error(\sprintf("%s - %s - [%s] %s \"%s\" - \"%s\": %s\n", empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_REAL_IP'], @$_SERVER['PHP_AUTH_USER'], \date('r'), $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], empty($this->request) ? 'no resource' : \http_build_query($this->request->getData()), $this->body['error']));
        }
    }
    private function setOutput()
    {
        $output = \ob_get_contents();
        \ob_end_clean();
        if ($output) {
            $this->body['output'] = $output;
        }
    }
    public function setContentType($content_type)
    {
        $this->content_type = $content_type;
    }
}
