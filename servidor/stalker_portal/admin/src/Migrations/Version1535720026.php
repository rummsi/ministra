<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720026 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('storages')->hasColumn('wowza_port')) {
            $this->addSql("ALTER TABLE `storages` ADD `wowza_port` VARCHAR(8) NOT NULL DEFAULT '';");
        }
        if (!$schema->getTable('storages')->hasColumn('wowza_app')) {
            $this->addSql("ALTER TABLE `storages` ADD `wowza_app` VARCHAR(128) NOT NULL DEFAULT '';");
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if ($schema->getTable('storages')->hasColumn('wowza_app')) {
            $this->addSql('ALTER TABLE `storages` DROP COLUMN `wowza_app`;');
        }
        if ($schema->getTable('storages')->hasColumn('wowza_app')) {
            $this->addSql('ALTER TABLE `storages` DROP COLUMN `wowza_port`;');
        }
    }
}
