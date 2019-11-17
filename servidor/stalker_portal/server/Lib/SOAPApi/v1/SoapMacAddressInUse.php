<?php

namespace Ministra\Lib\SOAPApi\v1;

class SoapMacAddressInUse extends \Ministra\Lib\SOAPApi\v1\SoapException
{
    public $faultcode = '7';
    public $faultstring = 'MAC address already in use';
}
