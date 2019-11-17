<?php

namespace Ministra\Lib\SOAPApi\v1;

class SoapException extends \SoapFault
{
    /**
     * @var string
     */
    public $faultactor;
    /**
     * @var mixed
     */
    public $detail;
    public function __construct($faultactor = null, $detail = null)
    {
        if ($faultactor) {
            $this->faultactor = $faultactor;
        }
        if ($detail) {
            $this->detail = $detail;
        }
    }
}
