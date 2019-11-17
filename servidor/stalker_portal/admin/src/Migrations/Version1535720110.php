<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720110 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('image_update_settings')->hasColumn('stb_group_id')) {
            $this->addSql('ALTER TABLE `image_update_settings` ADD COLUMN `stb_group_id` INT NOT NULL DEFAULT 0;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `image_update_settings` DROP COLUMN `stb_group_id`;
--
EOL
);
    }
}
