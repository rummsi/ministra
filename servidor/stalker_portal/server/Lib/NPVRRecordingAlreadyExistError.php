<?php

namespace Ministra\Lib;

class NPVRRecordingAlreadyExistError extends \Ministra\Lib\NPVRUserException
{
    public function __construct()
    {
        parent::__construct(\_('Recording for this channel already exist'));
    }
}
