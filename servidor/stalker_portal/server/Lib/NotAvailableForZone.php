<?php

namespace Ministra\Lib;

use Exception;
class NotAvailableForZone extends \Exception
{
    protected $code = 'not_available_for_zone';
}
