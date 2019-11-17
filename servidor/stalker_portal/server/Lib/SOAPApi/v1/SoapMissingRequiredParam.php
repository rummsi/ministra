<?php

namespace Ministra\Lib\SOAPApi\v1;

class SoapMissingRequiredParam extends \Ministra\Lib\SOAPApi\v1\SoapException
{
    public $faultcode = '5';
    public $faultstring = 'Missing required param';
}
