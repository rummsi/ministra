<?php

namespace Ministra\Admin\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Silex\Application;
class SilexPsrContainer implements \Ministra\Admin\Container\ContainerInterface
{
    private $application;
    public function __construct(\Silex\Application $application)
    {
        $this->application = $application;
    }
    public function get($id)
    {
        return $this->application->offsetGet($id);
    }
    public function set($id, $object)
    {
        $this->application->offsetSet($id, $object);
    }
    public function has($id)
    {
        return $this->application->offsetExists($id);
    }
}
