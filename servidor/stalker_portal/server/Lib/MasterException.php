<?php

namespace Ministra\Lib;

use Exception;
class MasterException extends \Exception
{
    protected $storage_name;
    public function __construct($message, $storage_name)
    {
        parent::__construct($message);
        $this->storage_name = $storage_name;
    }
    public function getStorageName()
    {
        return $this->storage_name;
    }
}
