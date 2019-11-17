<?php

namespace Ministra\Lib\SOAPApi\v1;

class SoapSubscriptionUpdateError extends \Ministra\Lib\SOAPApi\v1\SoapException
{
    public $faultcode = '6';
    public $faultstring = 'Subscription update error';
}
