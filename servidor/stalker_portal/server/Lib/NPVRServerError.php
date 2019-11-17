<?php

namespace Ministra\Lib;

class NPVRServerError extends \Ministra\Lib\NPVRServerException
{
    public function __construct()
    {
        parent::__construct(\_('Server error'));
    }
}
