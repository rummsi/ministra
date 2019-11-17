<?php

namespace Ministra\Lib\SOAPApi\v1;

class SoapServerError extends \Ministra\Lib\SOAPApi\v1\SoapException
{
    public $faultcode = '500';
    public $faultstring = 'Server error';
}
