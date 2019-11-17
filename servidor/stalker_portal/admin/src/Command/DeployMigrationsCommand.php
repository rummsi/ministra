<?php

namespace Ministra\Admin\Command;

use Doctrine\DBAL\Exception\TableNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
class DeployMigrationsCommand extends \Symfony\Component\Console\Command\Command
{
    use \Ministra\Admin\Command\ContainerTrait;
    private $rollbackMigration = null;
    protected function configure()
    {
        $this->setName('mtv:migrations:deploy')->setDescription('Deploy migrations command');
    }
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $maxRevision = (int) $this->getVersion();
        if (0 !== $this->runMigrationsCommand('migrations:migrate')) {
            $this->rollbackMigration = $maxRevision === 0 ? 'first' : null;
            $this->rollback($maxRevision ?: 0, $output);
            exit(1);
        }
        $output->writeln('Migrated successfully');
    }
    protected function rollback($maxRevision, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $revision = $this->getVersion(false, $maxRevision);
        if ($this->rollbackMigration) {
            $revision = $this->rollbackMigration;
        }
        if ($revision) {
            if (0 !== $this->runMigrationsCommand("migrations:migrate {$revision}")) {
                $output->writeln('Failed migration rollback command');
                exit(1);
            }
            $output->writeln('Failed run migration command');
            exit(1);
        }
        $output->writeln('Nothing rolling back');
    }
    protected function runMigrationsCommand($command)
    {
        $path = __DIR__ . '/../../bin/console';
        \system("php {$path} {$command} --no-interaction", $exitCode);
        return (int) $exitCode;
    }
    protected function getVersion($max = true, $to = null)
    {
        $migrationsConfig = $this->getContainer()->get('migrations.configuration');
        $query = $this->getConnection()->createQueryBuilder()->from($migrationsConfig->getMigrationsTableName());
        $aggregation = true === $max ? 'max' : 'min';
        $query = $query->select("{$aggregation}({$migrationsConfig->getMigrationsColumnName()})");
        if ($to) {
            $query = $query->where('version >= :version')->setParameter('version', $to);
        }
        try {
            return $query->orderBy('version', 'asc')->execute()->fetchColumn();
        } catch (\Doctrine\DBAL\Exception\TableNotFoundException $e) {
            return;
        }
    }
}
