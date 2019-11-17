<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720131 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('schedule_events')->hasColumn('channel')) {
            $this->addSql('ALTER TABLE `schedule_events` ADD COLUMN `channel` INT UNSIGNED NULL;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `schedule_events` DROP COLUMN `channel`;
--
EOL
);
    }
}
