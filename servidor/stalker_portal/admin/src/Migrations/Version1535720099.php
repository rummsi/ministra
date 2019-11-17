<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720099 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('launcher_apps')->hasColumn('options')) {
            $this->addSql('ALTER TABLE `launcher_apps` ADD COLUMN `options` TEXT;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `launcher_apps` DROP COLUMN `options`;
--
EOL
);
    }
}
