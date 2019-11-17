<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720087 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('vclub_not_ended')->hasColumn('file_id')) {
            $this->addSql('ALTER TABLE `vclub_not_ended` ADD COLUMN `file_id` int NOT NULL DEFAULT 0;');
        }
        if (!$schema->getTable('vclub_not_ended')->hasColumn('season_id')) {
            $this->addSql('ALTER TABLE `vclub_not_ended` ADD COLUMN `season_id` int NOT NULL DEFAULT 0;');
        }
        if (!$schema->getTable('vclub_not_ended')->hasColumn('episode_id')) {
            $this->addSql('ALTER TABLE `vclub_not_ended` ADD COLUMN `episode_id` int NOT NULL DEFAULT 0;');
        }
        if (!$schema->getTable('vclub_not_ended')->hasColumn('season')) {
            $this->addSql('ALTER TABLE `vclub_not_ended` ADD COLUMN `season` int NOT NULL DEFAULT 0;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `vclub_not_ended` DROP COLUMN `season`;
ALTER TABLE `vclub_not_ended` DROP COLUMN `episode_id`;
ALTER TABLE `vclub_not_ended` DROP COLUMN `season_id`;
ALTER TABLE `vclub_not_ended` DROP COLUMN `file_id`;
--
EOL
);
    }
}
