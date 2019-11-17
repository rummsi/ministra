<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720187 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('video')->hasColumn('admin_id')) {
            $this->addSql('ALTER TABLE `video` ADD COLUMN `admin_id` INTEGER DEFAULT null AFTER `added`;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if ($schema->getTable('video')->hasIndex('admin_id')) {
            $this->addSql(<<<EOL
ALTER TABLE `video` DROP INDEX `admin_id` ;
--
EOL
);
        }
        if ($schema->getTable('video')->hasColumn('admin_id')) {
            $this->addSql(<<<EOL
ALTER TABLE `video` DROP `admin_id`;
--
EOL
);
        }
    }
}
