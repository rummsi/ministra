<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719964 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('epg_setting')->hasColumn('id_prefix')) {
            $this->addSql("ALTER TABLE `epg_setting` ADD `id_prefix` VARCHAR(64) NOT NULL DEFAULT '';");
        }
        if (!$schema->getTable('users')->hasColumn('comment')) {
            $this->addSql('ALTER TABLE `users` ADD `comment` text not null;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users` DROP `comment`;
ALTER TABLE `epg_setting` DROP `id_prefix`;
--
EOL
);
    }
}
