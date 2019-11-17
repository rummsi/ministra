<?php

namespace Ministra\Lib;

class SmartLauncherAppsManagerConflictException extends \Ministra\Lib\SmartLauncherAppsManagerException
{
    protected $conflicts;
    public function __construct($message, $conflicts)
    {
        parent::__construct($message);
        $this->conflicts = $conflicts;
    }
    public function getConflicts()
    {
        return $this->conflicts;
    }
}
