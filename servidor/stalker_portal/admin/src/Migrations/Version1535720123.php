<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720123 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('users')->hasColumn('subtitle_color')) {
            $this->addSql('ALTER TABLE `users` ADD `subtitle_color` INT NOT NULL DEFAULT 16777215  AFTER `sec_subtitle_lang`;');
        }
        if (!$schema->getTable('users')->hasColumn('subtitle_size')) {
            $this->addSql('ALTER TABLE `users` ADD `subtitle_size` TINYINT NOT NULL DEFAULT 20 AFTER `sec_subtitle_lang`;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users` DROP COLUMN `subtitle_size`;
ALTER TABLE `users` DROP COLUMN `subtitle_color`;
--
EOL
);
    }
}
