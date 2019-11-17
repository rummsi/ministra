<?php

namespace Ministra\Lib\SOAPApi\v1;

class SoapWrongMacFormat extends \Ministra\Lib\SOAPApi\v1\SoapException
{
    public $faultcode = '2';
    public $faultstring = 'Wrong MAC address format';
}
