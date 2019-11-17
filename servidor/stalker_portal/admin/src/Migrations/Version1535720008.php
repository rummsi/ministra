<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720008 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('users')->hasColumn('play_in_preview_by_ok')) {
            $this->addSql('ALTER TABLE `users` ADD `play_in_preview_by_ok` tinyint DEFAULT NULL;');
        }
        if (!$schema->getTable('users')->hasColumn('show_after_loading')) {
            $this->addSql("ALTER TABLE `users` ADD `show_after_loading` varchar(16) NOT NULL default '';");
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users` DROP `show_after_loading`;
ALTER TABLE `users` DROP `play_in_preview_by_ok`;
--
EOL
);
    }
}
