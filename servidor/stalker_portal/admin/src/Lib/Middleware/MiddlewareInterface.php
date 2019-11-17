<?php

namespace Ministra\Admin\Lib\Middleware;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
interface MiddlewareInterface
{
    public function process(\Symfony\Component\HttpFoundation\Request $request, \Psr\Container\ContainerInterface $container);
}
