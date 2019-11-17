<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719974 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('itv')->hasColumn('allow_local_pvr')) {
            $this->addSql('ALTER TABLE `itv` ADD `allow_local_pvr` tinyint default 1;');
        }
        if (!$schema->getTable('users_rec')->hasColumn('internal_id')) {
            $this->addSql("ALTER TABLE `users_rec` ADD `internal_id` varchar(32) NOT NULL default '';");
        }
        if (!$schema->getTable('users_rec')->hasColumn('file')) {
            $this->addSql("ALTER TABLE `users_rec` ADD `file` varchar(255) NOT NULL default '';");
        }
        if (!$schema->getTable('users_rec')->hasColumn('local')) {
            $this->addSql('ALTER TABLE `users_rec` ADD `local` tinyint default 0;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users_rec` DROP `local`;
ALTER TABLE `users_rec` DROP `file`;
ALTER TABLE `users_rec` DROP `internal_id`;
ALTER TABLE `itv` DROP `allow_local_pvr`;
--
EOL
);
    }
}
