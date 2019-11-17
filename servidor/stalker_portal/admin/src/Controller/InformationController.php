<?php

namespace Ministra\Admin\Controller;

use Silex\Application;
class InformationController extends \Ministra\Admin\Controller\BaseMinistraController
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
}
