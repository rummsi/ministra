<?php

if (!isset($app)) {
    throw new \Exception('App variable does not define');
}
use Ministra\Admin\Lib\Middleware\Before\AdminMiddleware;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
$before = $app['pipelines']->setMiddleware([\Ministra\Admin\Lib\Middleware\Before\AdminMiddleware::class])->getClosure();
$after = function (\Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\HttpFoundation\Response $response, \Silex\Application $app) {
};
$app->get('/{controller}', function ($controller, $namespace = 'Ministra\\Admin\\Controller') use($app) {
    return \admin_controller_resolver($app, $namespace, $controller);
})->value('controller', 'index')->before($before)->after($after);
$app->get('/{controller}/{action}', function ($controller, $action, $namespace = 'Ministra\\Admin\\Controller') use($app) {
    return \admin_controller_resolver($app, $namespace, $controller, $action);
})->value('controller', 'index')->value('action', 'index')->before($before)->after($after);
$app->post('/{controller}', function ($controller, $namespace = 'Ministra\\Admin\\Controller') use($app) {
    return \admin_controller_resolver($app, $namespace, $controller);
})->value('controller', 'index')->before($before)->after($after);
$app->post('/{controller}/{action}', function ($controller, $action, $namespace = 'Ministra\\Admin\\Controller') use($app) {
    return \admin_controller_resolver($app, $namespace, $controller, $action);
})->value('controller', 'index')->value('action', 'index')->before($before)->after($after);
return $app;
