<?php

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
if (!\function_exists('admin_controller_resolver')) {
    function admin_controller_resolver()
    {
        $app = $namespace = $controller = $action = \null;
        $variables = ['app', 'namespace', 'controller', 'action'];
        $func_args = \func_get_args();
        foreach ($func_args as $key => $val) {
            ${$variables[$key]} = $val;
        }
        $action = !empty($action) ? \str_replace('-', '_', $action) : 'index';
        $controllerName = "\\{$namespace}\\" . \implode('', \array_map('ucfirst', \explode('-', \strtolower($controller)))) . 'Controller';
        if (\class_exists($controllerName) && \method_exists($controllerName, $action)) {
            $controller = new $controllerName($app);
            if ($controller instanceof $controllerName) {
                if (\is_callable([$controller, $action])) {
                    $reflection = new \ReflectionClass(\get_class($controller));
                    $method = $reflection->getMethod($action);
                    $parameters = $method->getParameters();
                    $request = $app['request_stack']->getCurrentRequest();
                    $resolved = [];
                    foreach ($parameters as $parameter) {
                        $class = $parameter->getClass();
                        if ($class) {
                            if ($class->getName() === \Silex\Application::class) {
                                $resolved[] = $app;
                            } else {
                                if ($class->getName() === \Symfony\Component\HttpFoundation\Request::class || \class_implements(\Symfony\Component\HttpFoundation\Request::class, $class->getName())) {
                                    $resolved[] = $request;
                                } else {
                                    $resolved[] = $app[$class->getName()];
                                }
                            }
                            continue;
                        }
                        $value = $request->get($parameter->getName(), \null);
                        if (\null === $value) {
                            if (!$parameter->isDefaultValueAvailable()) {
                                throw new \InvalidArgumentException("Argument `{$parameter->getName()}` for {$controllerName}::{$action} does not defined");
                            }
                            $value = $parameter->getDefaultValue();
                        }
                        $resolved[] = $value;
                    }
                    return \call_user_func_array([$controller, $action], $resolved);
                }
            }
        }
        return $app->abort(404, \sprintf('No route found for: %s:%s', $controllerName, $action));
    }
}
if (!\function_exists('optionsConcat')) {
    function optionsConcat(&$var, $fields = array(), $options = array())
    {
        foreach ($fields as $field) {
            if (!empty($options[$field])) {
                $var .= $options[$field];
            }
        }
    }
}
if (!\function_exists('adminLogError')) {
    function adminLogError(\Silex\Application $app, $e)
    {
        $message = $e->getMessage() . \PHP_EOL . $e->getTraceAsString();
        if ($app->offsetExists('monolog')) {
            $app->offsetGet('monolog')->error($message);
        } else {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\s11f4c3e4ac7fcef8584efe64e972b115::q6ee195c1759171b9aef09286fb44db47($message);
        }
    }
}
