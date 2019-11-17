<?php

namespace Ministra\Lib\HTTP;

class HTTPResponse implements \Ministra\Lib\HTTP\IHTTPResponse
{
    const STATUS_500 = '500 Internal Server Error';
    protected $status;
    protected $content_type;
    protected $body = '';
    public function setStatus($status)
    {
        $this->status = $status;
    }
    public function setContentType($content_type)
    {
        $this->content_type = $content_type;
    }
    public function setBody($body)
    {
        $this->body = $body;
    }
    public function send()
    {
        if (!empty($this->status)) {
            \header($_SERVER['SERVER_PROTOCOL'] . ' ' . $this->status);
        }
        if (!empty($this->content_type)) {
            \header('Content-type: ' . $this->content_type . '; charset=utf-8');
        }
        echo $this->body;
    }
}
