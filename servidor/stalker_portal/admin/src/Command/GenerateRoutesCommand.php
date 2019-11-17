<?php

namespace Ministra\Admin\Command;

use Ministra\Admin\Lib\Authentication\User\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
class GenerateRoutesCommand extends \Symfony\Component\Console\Command\Command
{
    use \Ministra\Admin\Command\ContainerTrait;
    protected $skipped = array('BaseMinistraController.php');
    protected $skipActions = array('set-localization', 'get-field-from-array', 'generate-ajax-response', 'check-last-location', 'group-post-action', 'group-message-list', 'set-s-q-l-debug');
    protected $url = 'http://localhost:880';
    protected function configure()
    {
        $this->setName('mtv:routes:generate')->addOption('url', 'u', \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'Base url for ministra TV PLatform')->addOption('file', 'f', \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'Output files for routes list')->setDescription('Generate routes list');
    }
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $controllersList = \glob(__DIR__ . '/../Controller/*.php');
        $this->url = $input->getOption('url') ?: $this->url;
        $file = $input->getOption('file') ?: __DIR__ . '/../../resources/routes.json';
        $log = \fopen(__DIR__ . '/../../logs/routes-log.logs', 'a');
        \fwrite($log, "\n\n" . \date('Y-m-d H:i:s') . "\n\n");
        $routes = [];
        foreach ($controllersList as $controller) {
            if (\in_array($controller, $this->skipped)) {
                continue;
            }
            $className = $this->getControllerName($controller);
            $reflection = new \ReflectionClass($className);
            $publicMethods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            $controllerUrl = $this->nameToUrl($this->getClassName($className));
            $routes[$controllerUrl] = [];
            $routesAll = [];
            \fwrite($log, "Next class is {$className}\n");
            foreach ($publicMethods as $method) {
                $action = $this->nameToUrl(\str_replace('_', '-', $method->getName()));
                if (\in_array($action, $this->skipActions)) {
                    continue;
                }
                if ($action === '--construct') {
                    continue;
                }
                \fwrite($log, "Check {$this->getUrl($controllerUrl, $action)}\n");
                if (!$this->checkRouteExists($controllerUrl, $action)) {
                    \fwrite($log, "Check {$this->getUrl($controllerUrl, $action)} failed\n");
                    continue;
                }
                \fwrite($log, "Check {$this->getUrl($controllerUrl, $action)} success \n");
                $routes[$controllerUrl][] = $action;
                $routesAll = $this->getUrl($controllerUrl, $action);
                $this->checkRouteExists($controllerUrl, $action);
            }
        }
        \fclose($log);
        \file_put_contents($file, \json_encode($routes));
        \file_put_contents($file . 'all.json', \json_encode($routesAll));
    }
    private function getUrl($controller, $action)
    {
        return \sprintf('%s/%s', $controller, $action);
    }
    private function checkRouteExists($controller, $action)
    {
        $request = new \Symfony\Component\HttpFoundation\Request([$url = $this->getUrl($controller, $action)]);
        $request->setMethod('GET');
        $session = $this->container['session'];
        $firewall = 'secured';
        $this->container['user'] = new \Ministra\Admin\Lib\Authentication\User\User(['username' => 'admin'], ['ROLE_ADMIN']);
        $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($this->container['user'], '1', $firewall, ['ROLE_ADMIN']);
        $this->container['security.token_storage']->setToken($token);
        $response = $this->container->handle($request);
        if ($response->getStatusCode() === 200) {
            return true;
        }
        \var_dump($url);
        \var_dump($response->getStatusCode());
        echo $response->getContent();
        exit;
    }
    private function nameToUrl($name)
    {
        $controllerUrl = \preg_replace('/(?<=\\w)(?=[A-Z])/', '-$1', $this->getClassName($name));
        return \mb_strtolower(\trim($controllerUrl));
    }
    private function getClassName($className)
    {
        return \str_replace('Controller', '', \str_replace('\\', '', $className));
    }
    private function getControllerName($fileName)
    {
        $basePath = __DIR__ . '/../';
        $fileName = \str_replace($basePath, '', $fileName);
        $fileName = \str_replace('.php', '', $fileName);
        return \str_replace('/', '\\', $fileName);
    }
}
