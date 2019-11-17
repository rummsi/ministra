<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version20190927081556 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $tables = $schema->getTables();
        foreach ($tables as $table) {
            $table->addOption('charset', 'utf8');
            $table->addOption('collate', 'utf8_general_ci');
            $this->addSql("ALTER TABLE `{$table->getName()}` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
    public function getDescription()
    {
        return 'Update all tables encoding';
    }
}
