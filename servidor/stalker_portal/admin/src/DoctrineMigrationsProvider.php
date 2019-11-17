<?php

namespace Ministra\Admin;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\DBAL\Migrations\Tools\Console\Command as MigrationsCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Symfony\Component\Console\Application as Console;
use Symfony\Component\Console\Helper as Helper;
use Symfony\Component\Console\Output\ConsoleOutput;
class DoctrineMigrationsProvider implements \Pimple\ServiceProviderInterface, \Silex\Api\BootableProviderInterface
{
    protected $console;
    public function __construct(\Symfony\Component\Console\Application $console = null)
    {
        $this->console = $console;
    }
    public function register(\Pimple\Container $app)
    {
        $app['migrations.output_writer'] = function (\Pimple\Container $app) {
            return new \Doctrine\DBAL\Migrations\OutputWriter(function ($message) {
                $output = new \Symfony\Component\Console\Output\ConsoleOutput();
                $output->writeln($message);
            });
        };
        $app['migrations.directory'] = null;
        $app['migrations.name'] = 'Migrations';
        $app['migrations.namespace'] = null;
        $app['migrations.table_name'] = 'migration_versions';
        $app['migrations.em_helper_set'] = function (\Pimple\Container $app) {
            $helpers = ['connection' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($app['db'])];
            if (\class_exists('\\Symfony\\Component\\Console\\Helper\\QuestionHelper')) {
                $helpers['question'] = new \Symfony\Component\Console\Helper\QuestionHelper();
            } else {
                if (\class_exists('\\Symfony\\Component\\Console\\Helper\\DialogHelper')) {
                    $helpers['dialog'] = new \Symfony\Component\Console\Helper\DialogHelper();
                }
            }
            if (isset($app['orm.em'])) {
                if (\class_exists('\\Doctrine\\ORM\\Tools\\Console\\Helper\\EntityManagerHelper')) {
                    $helpers['em'] = new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($app['orm.em']);
                }
            }
            return new \Symfony\Component\Console\Helper\HelperSet($helpers);
        };
        $app['migrations.configuration'] = function (\Pimple\Container $app) {
            $configuration = new \Doctrine\DBAL\Migrations\Configuration\Configuration($app['db'], $app['migrations.output_writer']);
            $configuration->setMigrationsDirectory($app['migrations.directory']);
            $configuration->setName($app['migrations.name']);
            $configuration->setMigrationsNamespace($app['migrations.namespace']);
            $configuration->setMigrationsTableName($app['migrations.table_name']);
            $configuration->registerMigrationsFromDirectory($app['migrations.directory']);
            return $configuration;
        };
        $app['migrations.command_names'] = function (\Pimple\Container $app) {
            $commands = [\Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand::class, \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand::class, \Doctrine\DBAL\Migrations\Tools\Console\Command\LatestCommand::class, \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand::class, \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand::class, \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand::class, \Doctrine\DBAL\Migrations\Tools\Console\Command\UpToDateCommand::class];
            $console = $this->getConsole($app);
            if ($console && true === $console->getHelperSet()->has('em')) {
                $commands[] = \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand::class;
            }
            return $commands;
        };
        $app['migrations.commands'] = function (\Pimple\Container $app) {
            $commands = [];
            foreach ($app['migrations.command_names'] as $name) {
                $command = new $name();
                $command->setMigrationConfiguration($app['migrations.configuration']);
                $commands[] = $command;
            }
            return $commands;
        };
    }
    public function boot(\Silex\Application $app)
    {
        $console = $this->getConsole($app);
        if ($console) {
            $helperSet = $console->getHelperSet();
            foreach ($app['migrations.em_helper_set'] as $name => $helper) {
                if (false === $helperSet->has($name)) {
                    $helperSet->set($helper, $name);
                }
            }
            $console->addCommands($app['migrations.commands']);
        }
    }
    public function getConsole(\Pimple\Container $app = null)
    {
        return $this->console ?: (isset($app['console']) ? $app['console'] : new \Symfony\Component\Console\Application());
    }
}
