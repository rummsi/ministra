<?php

namespace Ministra\Lib\Utils;

class FormatJSONP extends \Ministra\Lib\Utils\Format
{
    private $callback;
    public function __construct($array)
    {
        if (empty($this->callback)) {
            $this->callback = 'response_callback';
        }
        $this->formatted = $this->callback . '(' . \json_encode($array) . ')';
    }
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }
}
