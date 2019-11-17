<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720077 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('users')->hasColumn('blocked')) {
            $this->addSql('ALTER TABLE `users` ADD COLUMN `blocked` TINYINT NOT NULL DEFAULT 0;');
        }
        if (!$schema->getTable('users')->hasColumn('hw_version_2')) {
            $this->addSql("ALTER TABLE `users` ADD COLUMN `hw_version_2` VARCHAR(8) NOT NULL DEFAULT '';");
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users` DROP COLUMN `hw_version_2`;
ALTER TABLE `users` DROP COLUMN `blocked`;
--
EOL
);
    }
}
