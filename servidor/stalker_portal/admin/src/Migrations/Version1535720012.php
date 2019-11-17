<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720012 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('image_update_settings')->hasColumn('hardware_version_contains')) {
            $this->addSql("ALTER TABLE `image_update_settings` ADD `hardware_version_contains` varchar(32) NOT NULL default '';");
        }
        if (!$schema->getTable('users')->hasColumn('hw_version')) {
            $this->addSql("ALTER TABLE `users` ADD `hw_version` varchar(32) NOT NULL default '';");
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users` DROP `hw_version`;
ALTER TABLE `image_update_settings` DROP `hardware_version_contains`;
--
EOL
);
    }
}
