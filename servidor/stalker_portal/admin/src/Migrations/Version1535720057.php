<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720057 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('apps')->hasColumn('icon_color')) {
            $this->addSql("ALTER TABLE `apps` ADD COLUMN `icon_color` VARCHAR(16) NOT NULL DEFAULT '';");
        }
        if (!$schema->getTable('apps')->hasColumn('description')) {
            $this->addSql('ALTER TABLE `apps` ADD COLUMN `description` TEXT;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `apps` DROP COLUMN `description`;
ALTER TABLE `apps` DROP COLUMN `icon_color`;
--
EOL
);
    }
}
