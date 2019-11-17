<?php

namespace Ministra\Admin\Lib\Middleware\After;

use Ministra\Admin\Lib\Middleware\AbstractMiddleware;
use Psr\Container\ContainerInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class AfterAdminMiddleware extends \Ministra\Admin\Lib\Middleware\AbstractMiddleware
{
    protected $response;
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, \Silex\Application $app, \Symfony\Component\HttpFoundation\Response $response)
    {
        $this->setRequest($request);
        $this->setContainer($app);
        $this->response = $response;
    }
    public function process(\Symfony\Component\HttpFoundation\Request $request, \Psr\Container\ContainerInterface $container)
    {
    }
    public function generateAjaxResponse($data = array(), $error = '')
    {
        $response = [];
        if (!empty($this->request->request->has('for_validator'))) {
            $error = \trim($error);
            $response['valid'] = empty($error) && !empty($data);
            $response['message'] = \array_key_exists('chk_rezult', $data) ? \trim($data['chk_rezult']) : $error;
        } else {
            if (empty($error) && !empty($data)) {
                $response['success'] = true;
                $response['error'] = false;
            } else {
                $response['success'] = false;
                $response['error'] = $error;
            }
            $response = \array_merge($response, $data);
        }
        return $response;
    }
}
