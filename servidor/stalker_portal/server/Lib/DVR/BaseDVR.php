<?php

namespace Ministra\Lib\DVR;

class BaseDVR
{
    protected $valid;
    protected $stream;
    protected $ip;
    public function __construct($stream, $ip)
    {
        $this->stream = $stream;
        $this->ip = $ip;
    }
    public function isValid()
    {
        return $this->valid;
    }
}
