<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720143 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('administrators')->hasColumn('theme')) {
            $this->addSql("ALTER TABLE `administrators` ADD COLUMN `theme` VARCHAR(45) NOT NULL DEFAULT '';");
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `administrators` DROP COLUMN `theme`;
--
EOL
);
    }
}
