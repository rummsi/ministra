<?php

namespace Ministra\Admin\Controller;

use Silex\Application;
class LoginController extends \Ministra\Admin\Controller\BaseMinistraController
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
        return $this->app['twig']->render($this->getTemplateName(__METHOD__), ['error' => $this->app['security.last_error']($this->request), 'last_username' => $this->app['session']->get('_security.last_username')]);
    }
}
