<?php

namespace Ministra\Admin\Command;

use Psr\Container\ContainerInterface;
trait ContainerTrait
{
    protected $container;
    public function setContainer(\Psr\Container\ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }
    public function getContainer()
    {
        return $this->container;
    }
    public function getConnection()
    {
        return $this->container->get('db');
    }
}
