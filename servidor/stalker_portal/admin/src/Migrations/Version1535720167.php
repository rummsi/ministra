<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720167 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('settings')->hasColumn('default_launcher_template')) {
            $this->addSql("ALTER TABLE `settings` ADD `default_launcher_template` varchar(255) NOT NULL DEFAULT 'smart_launcher:magcore-theme-graphite';");
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `settings` DROP `default_launcher_template`;
--
EOL
);
    }
}
