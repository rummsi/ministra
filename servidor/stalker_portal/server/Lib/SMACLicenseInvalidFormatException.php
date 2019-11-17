<?php

namespace Ministra\Lib;

use Exception;
class SMACLicenseInvalidFormatException extends \Exception
{
    private $licenses = array();
    public function getLicenses()
    {
        return $this->licenses;
    }
    public function setLicenses($licenses)
    {
        $this->licenses = $licenses;
    }
    public function getLicensesAsString($delimiter = ', ')
    {
        return \implode($delimiter, $this->licenses);
    }
}
