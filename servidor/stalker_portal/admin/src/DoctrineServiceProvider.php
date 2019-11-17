<?php

namespace Ministra\Admin;

use Ministra\Admin\Doctrine\Configuration;
use Pimple\Container;
class DoctrineServiceProvider extends \Ministra\Admin\DoctrineMigrationsProvider
{
    public function register(\Pimple\Container $app)
    {
        $result = parent::register($app);
        $app['migrations.configuration'] = function (\Pimple\Container $app) {
            $configuration = new \Ministra\Admin\Doctrine\Configuration($app['db'], $app['migrations.output_writer']);
            $configuration->setMigrationsDirectory($app['migrations.directory']);
            $configuration->setName($app['migrations.name']);
            $configuration->setMigrationsNamespace($app['migrations.namespace']);
            $configuration->setMigrationsTableName($app['migrations.table_name']);
            $configuration->registerMigrationsFromDirectory($app['migrations.directory']);
            return $configuration;
        };
        return $result;
    }
}
