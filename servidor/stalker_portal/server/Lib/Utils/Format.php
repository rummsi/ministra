<?php

namespace Ministra\Lib\Utils;

abstract class Format implements \Ministra\Lib\Utils\IFormat
{
    protected $formatted;
    public function getOutput()
    {
        return $this->formatted;
    }
}
