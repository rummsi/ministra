<?php

namespace Ministra\Storage\Lib;

abstract class APIRequest
{
    public abstract function getAction();
    public abstract function getResource();
}
