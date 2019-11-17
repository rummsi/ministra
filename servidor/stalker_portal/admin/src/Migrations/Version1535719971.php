<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719971 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('users')->hasColumn('created')) {
            $this->addSql('ALTER TABLE `users` ADD `created` timestamp NULL DEFAULT NULL;');
        }
        if (!$schema->getTable('users')->hasColumn('last_watchdog')) {
            $this->addSql('ALTER TABLE `users` ADD `last_watchdog` timestamp NULL DEFAULT NULL;');
        }
        if (!$schema->getTable('users')->hasColumn('just_started')) {
            $this->addSql('ALTER TABLE `users` ADD `just_started` tinyint default 0;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users` DROP `just_started`;
ALTER TABLE `users` DROP `last_watchdog`;
ALTER TABLE `users` DROP `created`;
--
EOL
);
    }
}
