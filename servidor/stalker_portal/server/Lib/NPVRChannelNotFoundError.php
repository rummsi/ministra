<?php

namespace Ministra\Lib;

class NPVRChannelNotFoundError extends \Ministra\Lib\NPVRServerException
{
    public function __construct()
    {
        parent::__construct(\_('Channel not found'));
    }
}
