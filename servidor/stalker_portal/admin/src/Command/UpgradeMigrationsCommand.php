<?php

namespace Ministra\Admin\Command;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Ministra\Admin\Doctrine\Configuration as MigrationsConfigurations;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
class UpgradeMigrationsCommand extends \Symfony\Component\Console\Command\Command
{
    use \Ministra\Admin\Command\ContainerTrait;
    protected function configure()
    {
        $this->setDescription('Update migration version')->setName('mtv:migrations:upgrade');
    }
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $migrationsConfig = $this->getContainer()->get('migrations.configuration');
        $migrationsConfig->createMigrationTable();
        $migrations = $this->getAllItems($migrationsConfig);
        foreach ($migrations as $file => $migration) {
            if (isset(\Ministra\Admin\Doctrine\Configuration::$ALIASES[$file])) {
                $this->getConnection()->createQueryBuilder()->update($migrationsConfig->getMigrationsTableName(), 'c')->set('c.version', ':version')->set('c.origin_filename', ':file')->where('description = :description')->setParameter('description', $file)->setParameter('file', 'Version' . \Ministra\Admin\Doctrine\Configuration::$ALIASES[$file])->setParameter('version', \Ministra\Admin\Doctrine\Configuration::$ALIASES[$file])->execute();
            }
        }
    }
    private function getAllItems(\Doctrine\DBAL\Migrations\Configuration\Configuration $configuration)
    {
        $items = $this->getConnection()->createQueryBuilder()->select('*')->from($configuration->getMigrationsTableName())->execute()->fetchAll();
        $retItems = [];
        foreach ($items as $item) {
            $retItems[\trim($item['description'])] = $item;
        }
        return $retItems;
    }
}
