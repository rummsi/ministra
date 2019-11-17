<?php

namespace Ministra\Admin\Controller;

use Silex\Application;
class LogoutController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
        $this->app['error_local'] = [];
        $this->app['baseHost'] = $this->baseHost;
    }
    public function index()
    {
        $this->app['session']->clear();
        \session_destroy();
        return $this->app->redirect($this->workURL);
    }
}
