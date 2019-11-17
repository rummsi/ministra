<?php

namespace Ministra\Lib;

class StorageSessionLimitException extends \Ministra\Lib\MasterException
{
    public $message = 'Session limit';
    public function __construct($storage_name)
    {
        parent::__construct($this->message, $storage_name);
    }
}
