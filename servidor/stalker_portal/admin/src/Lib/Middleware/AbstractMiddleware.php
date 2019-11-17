<?php

namespace Ministra\Admin\Lib\Middleware;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
abstract class AbstractMiddleware implements \Ministra\Admin\Lib\Middleware\MiddlewareInterface
{
    protected $request;
    protected $container;
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = $this->container->get('request_stack')->getCurrentRequest();
        }
        return $this->request;
    }
    public function setRequest($request)
    {
        $this->request = $request;
    }
    public function getContainer()
    {
        return $this->container;
    }
    public function setContainer($container)
    {
        $this->container = $container;
    }
    public abstract function process(\Symfony\Component\HttpFoundation\Request $request, \Psr\Container\ContainerInterface $container);
    public static function getTemplateName($method_name, $extend = '', $namespace = __NAMESPACE__)
    {
        $method_name = \explode('::', \str_replace([$namespace, '\\'], '', $method_name));
        $method_name[] = \end($method_name);
        return '/' . \implode('/', $method_name) . $extend . '.twig';
    }
    protected function getForbidden(\Symfony\Component\HttpFoundation\Request $request)
    {
        if ($request && $request->isXmlHttpRequest()) {
            return static::getForbiddenAjaxResponse();
        }
        $content = $this->container->get('twig')->render(static::getTemplateName('AccessDenied::index'));
        return new \Symfony\Component\HttpFoundation\Response($content, 403);
    }
    public static function getForbiddenAjaxResponse($message = 'Access denied')
    {
        return new \Symfony\Component\HttpFoundation\JsonResponse(['valid' => false, 'message' => $message], 403);
    }
}
