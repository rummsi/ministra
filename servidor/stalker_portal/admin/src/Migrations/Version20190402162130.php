<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version20190402162130 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('user_log')->hasColumn('media_id')) {
            $this->addSql('ALTER TABLE `user_log` ADD COLUMN `media_id`  INT NULL DEFAULT 0;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql('ALTER TABLE `user_log` DROP COLUMN `media_id`;');
    }
    public function getDescription()
    {
        return 'Update user log';
    }
}
