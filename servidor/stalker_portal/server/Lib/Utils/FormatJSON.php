<?php

namespace Ministra\Lib\Utils;

class FormatJSON extends \Ministra\Lib\Utils\Format
{
    public function __construct($array)
    {
        $this->formatted = \json_encode($array);
    }
}
