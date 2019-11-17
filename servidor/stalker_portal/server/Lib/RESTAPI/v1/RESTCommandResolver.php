<?php

namespace Ministra\Lib\RESTAPI\v1;

class RESTCommandResolver
{
    public function __construct()
    {
    }
    public function getCommand(\Ministra\Lib\RESTAPI\v1\RESTRequest $request)
    {
        $resource = \implode('', \array_map(function ($part) {
            return \ucfirst($part);
        }, \explode('_', $request->getResource())));
        $class = 'Ministra\\Lib\\RESTAPI\\v1\\RESTCommand' . \ucfirst($resource);
        if (!\class_exists($class)) {
            throw new \Ministra\Lib\RESTAPI\v1\RESTCommandResolverException('Resource "' . $resource . '" does not exist');
        }
        return new $class();
    }
}
