<?php

namespace Ministra\Admin\Lib\Authentication;

use Ministra\Admin\Container\ContainerInterface;
use Ministra\Admin\Lib\Authentication\User\UserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
class AccessVoter implements \Symfony\Component\Security\Core\Authorization\Voter\VoterInterface
{
    private $container;
    private $accessHashMap = array('get' => array(0x0 => false, 0x1 => true, 0x2 => true, 0x3 => true, 0x4 => true, 0x5 => false, 0x6 => true, 0x7 => true), 'ajax_get' => array(0x0 => false, 0x1 => false, 0x2 => false, 0x3 => false, 0x4 => true, 0x5 => true, 0x6 => true, 0x7 => true), 'post' => array(0x0 => false, 0x1 => false, 0x2 => true, 0x3 => true, 0x4 => false, 0x5 => false, 0x6 => true, 0x7 => true), 'ajax_post' => array(0x0 => false, 0x1 => false, 0x2 => false, 0x3 => false, 0x4 => true, 0x5 => true, 0x6 => true, 0x7 => true));
    private $allowedAllControllers = array('login', 'auth-user', 'login-check');
    public function __construct(\Ministra\Admin\Container\ContainerInterface $container, $allowedAllControllers = null, $accessHashMap = null)
    {
        $this->container = $container;
        if (\is_array($accessHashMap)) {
            $this->accessHashMap = $accessHashMap;
        }
        if (\is_array($allowedAllControllers)) {
            $this->allowedAllControllers = $allowedAllControllers;
        }
    }
    public function supportsAttribute($attribute)
    {
    }
    public function supportsClass($class)
    {
    }
    public function getRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }
    public function vote(\Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token, $object, array $attributes)
    {
        if ($token->getUser()->getUsername() === \Ministra\Admin\Lib\Authentication\User\UserProvider::SUPER_USER_NAME) {
            return self::ACCESS_GRANTED;
        }
        $request = $this->getRequest();
        $requestPath = \mb_strtolower($this->getPathFromRequest($request));
        $pathParts = \explode('/', \trim($requestPath, '/'));
        $controller = $pathParts[0] ?: 'index';
        $action = '';
        if (isset($pathParts[1]) && !\is_numeric($pathParts[1])) {
            $actionParts = \explode('?', $pathParts[1] === 'index' ? '' : $pathParts[1]);
            $action = $actionParts[0];
        }
        $requestPath = $action ? "{$controller}/{$action}" : $controller;
        if (\in_array($controller, $this->allowedAllControllers)) {
            return self::ACCESS_GRANTED;
        }
        if (!$token->isAuthenticated() || !$request) {
            return self::ACCESS_DENIED;
        }
        $map = $this->container->get('access_map');
        $map->setUser($token->getUser());
        $map->setAccessMap();
        $mapConfig = $map->getAccessMap();
        $mainRole = \current($token->getUser()->getRoles());
        if (!\in_array($mainRole, \array_keys($mapConfig))) {
            return self::ACCESS_GRANTED;
        }
        $userConfig = $mapConfig[$mainRole];
        foreach ($userConfig as $path => $config) {
            if ($path === $requestPath) {
                if ($token->getUser()->getUsername() !== \Ministra\Admin\Lib\Authentication\User\UserProvider::SUPER_USER_NAME && $config['only_top_admin'] === '1') {
                    return self::ACCESS_DENIED;
                }
                $result = $this->checkActionPermissions($config, $userConfig, $controller, $action, $request->getMethod());
                return $result ? self::ACCESS_GRANTED : self::ACCESS_DENIED;
            }
        }
        return self::ACCESS_DENIED;
    }
    private function checkActionPermissions($config, $userConfig, $controller, $action, $method)
    {
        if ($this->checkActionPermission($this->getRequest()->isXmlHttpRequest(), $config['access'], $method)) {
            return true;
        }
        if (!\strlen($action)) {
            return false;
        }
        foreach (['/(-json|-list-json)$/', '/-json$/'] as $regular) {
            $currentAction = \preg_replace($regular, '', $action);
            if ($currentAction === $action) {
                continue;
            }
            foreach (["{$controller}/{$currentAction}", "{$currentAction}", "{$controller}"] as $nextAction) {
                if (!isset($userConfig[$nextAction])) {
                    continue;
                }
                $parentConfig = $userConfig[$nextAction];
                if ($parentConfig['only_top_admin'] == 1) {
                    continue;
                }
                if ($this->checkActionPermission($this->getRequest()->isXmlHttpRequest(), $parentConfig['access'] | '100', $method)) {
                    return true;
                }
            }
        }
        return false;
    }
    private function checkActionPermission($isAjax, $access, $method)
    {
        $mask = \bindec($access);
        $method = \strtolower($isAjax ? "ajax_{$method}" : $method);
        if (!isset($this->accessHashMap[$method]) || isset($this->accessHashMap[$method]) && !isset($this->accessHashMap[$method][$mask])) {
            return false;
        }
        return $this->accessHashMap[$method][$mask];
    }
    private function getPathFromRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        $parts = \explode('/', \trim($request->getPathInfo(), '/'));
        $controller = \mb_strtolower($parts[0]);
        $actionName = '';
        if (isset($parts[1]) && !\is_numeric($parts[1])) {
            $actionName = \mb_strtolower($parts[1]);
            $actionParts = \explode('?', $actionName);
            $actionName = $actionParts[0];
        }
        return $controller . '/' . $actionName;
    }
}
