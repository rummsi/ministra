<?php

namespace Ministra\Lib;

class Acl
{
    private $default_actions = array('view', 'edit', 'add', 'delete');
    private $user;
    public function __construct($user)
    {
        $this->user = $user;
    }
    public function addRole($name, $parent)
    {
    }
    public function addResource($name)
    {
    }
    public function allow($role, $resource, $right)
    {
    }
    public function disallow($role, $resource, $right)
    {
    }
    public function isAllowed($role, $resource, $action)
    {
    }
}
