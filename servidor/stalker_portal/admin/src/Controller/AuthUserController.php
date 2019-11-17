<?php

namespace Ministra\Admin\Controller;

use Silex\Application;
class AuthUserController extends \Ministra\Admin\Controller\BaseMinistraController
{
    protected $db;
    public function __construct(\Silex\Application $app)
    {
        parent::__construct($app, __CLASS__);
    }
    public function index()
    {
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function auth_user_profile()
    {
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function auth_user_messages()
    {
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function auth_user_tasks()
    {
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function auth_user_settings()
    {
        return $this->app['twig']->render($this->getTemplateName(__METHOD__));
    }
    public function auth_user_logout()
    {
        $this->app['session']->clear();
        \session_destroy();
        return $this->app->redirect($this->workURL . '/login', 302);
    }
}
