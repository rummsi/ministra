<?php

namespace Ministra\Lib;

use Exception;
class KinopoiskException extends \Exception
{
    protected $response;
    public function __construct($message, $response = '')
    {
        parent::__construct($message);
        $this->response = $response;
    }
    public function getResponse()
    {
        return $this->response;
    }
}
