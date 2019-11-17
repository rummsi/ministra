<?php

require __DIR__ . '/autoload.php';
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Ministra\Admin\Command\AnalyzingRoutesCommand;
use Ministra\Admin\Command\ClearCacheCommand;
use Ministra\Admin\Command\DeployMigrationsCommand;
use Ministra\Admin\Command\GenerateRoutesCommand;
use Ministra\Admin\Command\UpdateClearUtilCommand;
use Ministra\Admin\Command\UpgradeMigrationsCommand;
use Ministra\Admin\DoctrineServiceProvider;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\adeb59550d83526eb750f39a310123f7\a25cb0c656ab98dadb2f611ef8018492;
use Silex\Provider\SerializerServiceProvider;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
require __DIR__ . '/../app.php';
if (isset($app)) {
    $app->register(new \Silex\Provider\SerializerServiceProvider());
    $cli = new \Symfony\Component\Console\Application();
    $db = $app['db'];
    $app->register(new \Ministra\Admin\DoctrineServiceProvider($cli), ['migrations.directory' => __DIR__ . '/../src/Migrations', 'migrations.name' => 'Ministra DB Migrations', 'migrations.namespace' => 'Ministra\\Migrations', 'migrations.table_name' => 'changelog']);
    $helperSet = new \Symfony\Component\Console\Helper\HelperSet(['connection' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($app['db']), 'dialog' => new \Symfony\Component\Console\Helper\QuestionHelper()]);
    require __DIR__ . '/../config/boot_app.php';
    $cli->setHelperSet($helperSet);
    $cli->add((new \Ministra\Admin\Command\GenerateRoutesCommand())->setContainer($app[\Psr\Container\ContainerInterface::class]));
    $cli->add(new \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\adeb59550d83526eb750f39a310123f7\a25cb0c656ab98dadb2f611ef8018492());
    $cli->add((new \Ministra\Admin\Command\AnalyzingRoutesCommand())->setContainer($app[\Psr\Container\ContainerInterface::class]));
    $cli->add((new \Ministra\Admin\Command\UpgradeMigrationsCommand())->setContainer($app[\Psr\Container\ContainerInterface::class]));
    $cli->add((new \Ministra\Admin\Command\DeployMigrationsCommand())->setContainer($app[\Psr\Container\ContainerInterface::class]));
    $cli->add((new \Ministra\Admin\Command\UpdateClearUtilCommand())->setContainer($app[\Psr\Container\ContainerInterface::class]));
    $cli->add((new \Ministra\Admin\Command\ClearCacheCommand())->setContainer($app[\Psr\Container\ContainerInterface::class]));
    $cli->run();
}
