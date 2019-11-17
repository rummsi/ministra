<?php

namespace Ministra\Lib;

class NPVRTotalLengthLimitError extends \Ministra\Lib\NPVRUserException
{
    public function __construct()
    {
        parent::__construct(\_('Recording duration limit is reached'));
    }
}
