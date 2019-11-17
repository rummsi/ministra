<?php

namespace Ministra\Storage\Lib;

class RESTCommand extends \Ministra\Storage\Lib\APICommand
{
    public function doExecute(\Ministra\Storage\Lib\APIRequest $request)
    {
        $action = $request->getAction();
        if (!\is_callable([$this, $action])) {
            throw new \Ministra\Storage\Lib\RESTCommandException('Resource "' . $request->getResource() . '" does not support action "' . $action . '"');
        }
        return \call_user_func([$this, $action], $request);
    }
}
