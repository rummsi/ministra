<?php

namespace Ministra\Admin\Doctrine;

use Doctrine\DBAL\Migrations\Configuration\Configuration as MigrationsConfiguration;
use Doctrine\DBAL\Migrations\Provider\SchemaDiffProviderInterface;
use Doctrine\DBAL\Migrations\Version;
class MigrationVersion extends \Doctrine\DBAL\Migrations\Version
{
    protected $class;
    public function __construct(\Doctrine\DBAL\Migrations\Configuration\Configuration $configuration, $version, $class, \Doctrine\DBAL\Migrations\Provider\SchemaDiffProviderInterface $schemaProvider = null)
    {
        parent::__construct($configuration, $version, $class, $schemaProvider);
        $this->class = $class;
    }
    public function markMigrated()
    {
        $this->registerMigration();
    }
    protected function registerMigration()
    {
        $this->getConfiguration()->createMigrationTable();
        $filename = \basename(\str_replace('\\', '/', $this->class));
        $max = $this->getConfiguration()->getConnection()->createQueryBuilder()->select('max(change_number)')->from($this->getConfiguration()->getMigrationsTableName())->execute()->fetchColumn();
        $originFile = $this->getConfiguration()->findDescriptionByTime($this->getVersion()) ?: $filename;
        $this->getConfiguration()->getConnection()->insert($this->getConfiguration()->getMigrationsTableName(), [$this->getConfiguration()->getMigrationsColumnName() => $this->getVersion(), 'change_number' => (int) $max + 1, 'delta_set' => 'doctrine', 'start_dt' => \date('Y-m-d H:i:s', \time() - 100), 'complete_dt' => \date('Y-m-d H:i:s'), 'applied_by' => 'doctrine migrations command', 'description' => $originFile, 'origin_filename' => $filename, 'migration_description' => $this->getMigration()->getDescription()]);
    }
}
