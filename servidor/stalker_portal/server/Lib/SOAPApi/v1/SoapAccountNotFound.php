<?php

namespace Ministra\Lib\SOAPApi\v1;

class SoapAccountNotFound extends \Ministra\Lib\SOAPApi\v1\SoapException
{
    public $faultcode = '4';
    public $faultstring = 'Account not found';
}
