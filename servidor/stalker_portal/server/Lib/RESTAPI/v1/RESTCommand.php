<?php

namespace Ministra\Lib\RESTAPI\v1;

class RESTCommand extends \Ministra\Lib\RESTAPI\v1\APICommand
{
    public function doExecute(\Ministra\Lib\RESTAPI\v1\APIRequest $request)
    {
        $action = $request->getAction();
        if (!\is_callable([$this, $action])) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandException('Resource "' . $request->getResource() . '" does not support action "' . $action . '"');
        }
        return \call_user_func([$this, $action], $request);
    }
}
