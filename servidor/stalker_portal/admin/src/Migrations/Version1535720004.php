<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
class Version1535720004 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('itv')->hasColumn('tv_archive_duration')) {
            $this->addSql("ALTER TABLE `itv` ADD `tv_archive_duration` int not null default {$this->getDuration()};");
        }
    }
    protected function getDuration()
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('tv_archive_parts_number', 168);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `itv` DROP `tv_archive_duration`;
--
EOL
);
    }
}
