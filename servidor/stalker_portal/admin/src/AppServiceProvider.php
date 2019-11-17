<?php

namespace Ministra\Admin;

use Doctrine\DBAL\DriverManager;
use Ministra\Admin\Adapter\DataTableAdapter;
use Ministra\Admin\Container\SilexPsrContainer;
use Ministra\Admin\Lib\Middleware\Pipelines;
use Ministra\Admin\Repository\LicenseKeysRepository;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\M84db40638aa91895c6a5631406a35541;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\a7f116349ec7353d1ce30736006aa2a7\eb91b1f2a5b3360242363ae995e2ae37;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\p135e57b8e6f0c2ac96a99a6503178831 as r7356054e385a246acd7990cd79aeda1d;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\k5f3a0eff0bb2b863dd6257af9a557248\Y56cd1c67a10517284c5ec244883e0fef;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\k5f3a0eff0bb2b863dd6257af9a557248\c04a7c94f7ae7e0feb659afde12d3229;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\a1d0f8bc3ea86fe7395988b56c201809c;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Silex\Application;
class AppServiceProvider implements \Pimple\ServiceProviderInterface
{
    public function register(\Pimple\Container $container)
    {
        $container->offsetSet(\Psr\Container\ContainerInterface::class, function (\Silex\Application $app) {
            return new \Ministra\Admin\Container\SilexPsrContainer($app);
        });
        $container->offsetSet('pipelines', function (\Silex\Application $app) {
            return new \Ministra\Admin\Lib\Middleware\Pipelines();
        });
        $this->registerRepository($container);
        $container->offsetSet(\Ministra\Admin\Adapter\DataTableAdapter::class, function (\Silex\Application $app) {
            return new \Ministra\Admin\Adapter\DataTableAdapter($app['request_stack']->getCurrentRequest(), $app['db']);
        });
        $this->registerUtilConnection($container);
        $this->registerUtilService($container);
    }
    protected function registerRepository(\Pimple\Container $container)
    {
        $container->offsetSet(\Ministra\Admin\Repository\LicenseKeysRepository::class, function (\Silex\Application $app) {
            return new \Ministra\Admin\Repository\LicenseKeysRepository($app['db']);
        });
    }
    protected function registerUtilConnection(\Pimple\Container $container)
    {
        $file = \realpath(__DIR__ . '/../../') . '/deploy/clear_key_util/db_util.sqlite';
        if (!\file_exists($file)) {
            \file_put_contents($file, '');
        }
        if (!\is_writable($file)) {
            throw new \RuntimeException("Clear key util database file does not writable: {$file}");
        }
        $dirs = [\dirname($file) . '/logs', \dirname($file) . '/reports'];
        foreach ($dirs as $dir) {
            if (!\is_writable($file)) {
                throw new \RuntimeException("Directory does not writable: {$dir}");
            }
        }
        if (!\extension_loaded('pdo_sqlite')) {
            throw new \Exception('SQLite extension missing');
        }
        $container->offsetSet('util.connection', \Doctrine\DBAL\DriverManager::getConnection(['driver' => 'pdo_sqlite', 'path' => $file]));
    }
    protected function registerUtilService(\Pimple\Container $container)
    {
        $container->offsetSet(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\k5f3a0eff0bb2b863dd6257af9a557248\c04a7c94f7ae7e0feb659afde12d3229::class, function (\Silex\Application $app) {
            return new \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\k5f3a0eff0bb2b863dd6257af9a557248\c04a7c94f7ae7e0feb659afde12d3229($app['util.connection']);
        });
        $container->offsetSet(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\k5f3a0eff0bb2b863dd6257af9a557248\Y56cd1c67a10517284c5ec244883e0fef::class, function (\Silex\Application $app) {
            return new \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\k5f3a0eff0bb2b863dd6257af9a557248\Y56cd1c67a10517284c5ec244883e0fef($app['util.connection']);
        });
        $container->offsetSet(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\a7f116349ec7353d1ce30736006aa2a7\eb91b1f2a5b3360242363ae995e2ae37::class, function (\Silex\Application $app) {
            return new \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\p135e57b8e6f0c2ac96a99a6503178831($app['db']);
        });
        $container->offsetSet(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\M84db40638aa91895c6a5631406a35541::class, function (\Silex\Application $app) {
            return new \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\M84db40638aa91895c6a5631406a35541($app->offsetGet(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\k5f3a0eff0bb2b863dd6257af9a557248\Y56cd1c67a10517284c5ec244883e0fef::class), $app->offsetGet(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\k5f3a0eff0bb2b863dd6257af9a557248\c04a7c94f7ae7e0feb659afde12d3229::class), $app->offsetGet(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\a7f116349ec7353d1ce30736006aa2a7\eb91b1f2a5b3360242363ae995e2ae37::class), \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\j7648667849891a00de692bb49d55c4c6\a1d0f8bc3ea86fe7395988b56c201809c::a7acf30747f19491c2665bb0e507ee0d, $app->offsetGet('util.path'));
        });
    }
}
