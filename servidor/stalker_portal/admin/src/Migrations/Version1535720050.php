<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720050 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('apps')->hasColumn('alias')) {
            $this->addSql("ALTER TABLE `apps` ADD COLUMN `alias` VARCHAR(64) NOT NULL DEFAULT '';");
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `apps` DROP COLUMN `alias`;
--
EOL
);
    }
}
