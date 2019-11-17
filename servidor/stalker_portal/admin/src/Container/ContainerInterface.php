<?php

namespace Ministra\Admin\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;
interface ContainerInterface extends \Psr\Container\ContainerInterface
{
    public function set($name, $value);
}
