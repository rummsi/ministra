<?php

namespace Ministra\Lib\RESTAPI\v1;

abstract class APICommand
{
    public function execute(\Ministra\Lib\RESTAPI\v1\APIRequest $request)
    {
        return $this->doExecute($request);
    }
    public abstract function doExecute(\Ministra\Lib\RESTAPI\v1\APIRequest $request);
}
