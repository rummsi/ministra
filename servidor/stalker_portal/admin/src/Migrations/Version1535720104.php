<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720104 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('launcher_apps')->hasColumn('available_version')) {
            $this->addSql("ALTER TABLE `launcher_apps` ADD COLUMN `available_version` varchar(16) NOT NULL DEFAULT '' AFTER `current_version`;");
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `launcher_apps` DROP COLUMN `available_version`;
--
EOL
);
    }
}
