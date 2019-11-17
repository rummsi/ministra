<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719946 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('users')->hasColumn('tv_quality')) {
            $this->addSql("ALTER TABLE `users` ADD `tv_quality` varchar(16) default 'high';");
        }
        if (!$schema->getTable('itv')->hasColumn('quality')) {
            $this->addSql("ALTER TABLE `itv` ADD `quality` varchar(16) default 'high';");
        }
        if (!$schema->getTable('itv')->hasColumn('enable_wowza_load_balancing')) {
            $this->addSql('ALTER TABLE `itv` ADD `enable_wowza_load_balancing` tinyint default 0;');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `itv` DROP `enable_wowza_load_balancing`;
ALTER TABLE `itv` DROP `quality`;
ALTER TABLE `users` DROP `tv_quality`;
--
EOL
);
    }
}
