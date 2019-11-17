<?php

namespace Ministra\Storage\Lib;

abstract class APICommand
{
    public function execute(\Ministra\Storage\Lib\APIRequest $request)
    {
        return $this->doExecute($request);
    }
    public abstract function doExecute(\Ministra\Storage\Lib\APIRequest $request);
}
