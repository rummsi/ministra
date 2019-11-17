<?php

namespace Ministra\Admin\Lib;

class Base
{
    protected $app;
    protected $error = null;
    public function __construct($app)
    {
        $this->app = $app;
    }
    public function getError()
    {
        return $this->error;
    }
}
