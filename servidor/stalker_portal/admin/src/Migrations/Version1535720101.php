<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720101 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('launcher_apps')->hasColumn('manual_install')) {
            $this->addSql('ALTER TABLE `launcher_apps` ADD COLUMN `manual_install` TINYINT NOT NULL DEFAULT 0;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `launcher_apps` DROP COLUMN `manual_install`;
--
EOL
);
    }
}
