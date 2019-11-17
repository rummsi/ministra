<?php

namespace Ministra\Lib\RESTAPI\v1;

abstract class APIRequest
{
    public abstract function getAction();
    public abstract function getResource();
}
