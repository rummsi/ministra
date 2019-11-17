<?php

namespace Ministra\Admin\Lib\Middleware;

use Psr\Container\ContainerInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
class Pipelines
{
    private $middleware = array();
    private $middlewareInstances = array();
    private $request;
    private $app;
    public function setMiddleware($middleware)
    {
        if (!\is_array($middleware)) {
            throw new \InvalidArgumentException('Middleware is not array list');
        }
        $this->middleware = $middleware;
        return $this;
    }
    private function createMiddleware()
    {
        foreach ($this->middleware as $item) {
            if (isset($this->middlewareInstances[$item])) {
                continue;
            }
            $this->middlewareInstances[$item] = new $item($this->request, $this->app->offsetGet(\Psr\Container\ContainerInterface::class));
        }
        return $this->middlewareInstances;
    }
    public function getClosure()
    {
        return function (\Symfony\Component\HttpFoundation\Request $request, \Silex\Application $app) {
            $this->request = $request;
            $this->app = $app;
            foreach ($this->createMiddleware() as $next) {
                $resp = $next->process($request, $app->offsetGet(\Psr\Container\ContainerInterface::class));
                if (null !== $resp) {
                    return $resp;
                }
            }
        };
    }
}
